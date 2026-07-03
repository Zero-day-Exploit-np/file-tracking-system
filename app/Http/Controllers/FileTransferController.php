<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;
use App\Notifications\FileTransferredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FileTransferController extends Controller
{
    /**
     * Show the transfer form for a file.
     * Only the current holder (role:user) may transfer.
     */
    public function create(FileRecord $file)
    {
        $this->authorize('transfer', $file);

        $currentUser = Auth::user();

        // Users in the same department (for "Same Department" transfer)
        $sameDeptUsers = User::where('department_id', $currentUser->department_id)
            ->where('id', '!=', $currentUser->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('files.transfer', compact('file', 'sameDeptUsers'));
    }

    /**
     * Execute an immediate file transfer — no approval, no pending state.
     *
     * destination_type = 'same' | 'other'
     * If same:  to_user_id required (must be in same dept)
     * If other: department_id required (must be an existing dept in DB)
     */
    public function store(Request $request)
    {
        $request->validate([
            'file_record_uuid'  => 'required|string|exists:file_records,uuid',
            'destination_type'  => 'required|in:same,other',
            'to_user_id'        => 'required_if:destination_type,same|nullable|integer|exists:users,id',
            'department_id'     => 'required_if:destination_type,other|nullable|integer|exists:departments,id',
            'remarks'           => 'nullable|string|max:500',
        ]);

        $file        = FileRecord::where('uuid', $request->file_record_uuid)->firstOrFail();
        $currentUser = Auth::user();

        // Auth check
        $this->authorize('transfer', $file);

        $remarks = $request->string('remarks')->trim()->value() ?: null;

        if ($request->destination_type === 'same') {
            return $this->transferToUser($file, $currentUser, (int) $request->to_user_id, $remarks);
        }

        return $this->transferToDepartment($file, $currentUser, (int) $request->department_id, $remarks);
    }

    /**
     * AJAX: search departments by name for autocomplete.
     */
    public function searchDepartments(Request $request)
    {
        $q = $request->string('q')->trim()->value();

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $departments = Department::where('name', 'like', "%{$q}%")
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    private function transferToUser(FileRecord $file, User $currentUser, int $toUserId, ?string $remarks): \Illuminate\Http\RedirectResponse
    {
        $targetUser = User::findOrFail($toUserId);

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'You cannot transfer a file to yourself.');
        }

        // Must be same department
        if ((int) $targetUser->department_id !== (int) $currentUser->department_id) {
            return back()->with('error', 'Selected user is not in your department. Use "Other Department" for cross-department transfers.');
        }

        $transfer = null;

        DB::transaction(function () use ($file, $currentUser, $targetUser, $remarks, &$transfer) {
            $transfer = FileTransfer::create([
                'file_id'        => $file->id,
                'sender_id'      => $currentUser->id,
                'receiver_id'    => $targetUser->id,
                'remarks'        => $remarks,
                'transferred_at' => now(),
            ]);

            FileMovement::create([
                'file_id'         => $file->id,
                'from_user'       => $currentUser->id,
                'to_user'         => $targetUser->id,
                'from_department' => $currentUser->department_id,
                'to_department'   => $targetUser->department_id,
                'action'          => 'transferred',
                'remarks'         => $remarks ?? 'Same-department transfer',
            ]);

            $file->update([
                'current_user_id' => $targetUser->id,
                'status'          => 'active',
            ]);
        });

        if ($transfer) {
            $targetUser->notify(new FileTransferredNotification($transfer));
            event(new \App\Events\FileTransferred($transfer));
        }

        \App\Services\DashboardService::clearUserCache($currentUser->id);
        \App\Services\DashboardService::clearUserCache($targetUser->id);

        return redirect()->route('files.index')
            ->with('success', 'File transferred successfully to ' . $targetUser->name . '.');
    }

    private function transferToDepartment(FileRecord $file, User $currentUser, int $deptId, ?string $remarks): \Illuminate\Http\RedirectResponse
    {
        $targetDept = Department::findOrFail($deptId);

        if ((int) $targetDept->id === (int) $currentUser->department_id) {
            return back()->with('error', 'That is your own department. Use "Same Department" instead.');
        }

        // Find the best receiving user in the target department.
        // Preference: admin of that dept → any active user in that dept → null (dept has no users yet)
        $receiver = User::where('department_id', $targetDept->id)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->first()
            ?? User::where('department_id', $targetDept->id)
                ->where('is_active', true)
                ->first();

        $receiverId = $receiver?->id; // may be null if dept has no users

        $transfer = null;

        DB::transaction(function () use ($file, $currentUser, $targetDept, $receiver, $receiverId, $remarks, &$transfer) {
            // receiver_id is now nullable in file_transfers
            $transfer = FileTransfer::create([
                'file_id'        => $file->id,
                'sender_id'      => $currentUser->id,
                'receiver_id'    => $receiverId,
                'remarks'        => $remarks,
                'transferred_at' => now(),
            ]);

            FileMovement::create([
                'file_id'         => $file->id,
                'from_user'       => $currentUser->id,
                'to_user'         => $receiverId,  // nullable — dept-only transfer
                'from_department' => $currentUser->department_id,
                'to_department'   => $targetDept->id,
                'action'          => 'transferred',
                'remarks'         => $remarks ?? 'Cross-department transfer to ' . $targetDept->name,
            ]);

            $file->update([
                'current_user_id' => $receiverId,  // nullable — assigned later when dept user picks it up
                'department_id'   => $targetDept->id,
                'status'          => 'active',
            ]);
        });

        // Only notify and fire event if there is an actual receiver user
        if ($transfer && $receiver) {
            $receiver->notify(new FileTransferredNotification($transfer));
            // Only broadcast if receiver_id is set (event channel needs a real user id)
            event(new \App\Events\FileTransferred($transfer));
        }

        \App\Services\DashboardService::clearUserCache($currentUser->id);
        if ($receiver) {
            \App\Services\DashboardService::clearUserCache($receiver->id);
        }
        \App\Services\DashboardService::clearAdminCache($currentUser->department_id);
        \App\Services\DashboardService::clearAdminCache($targetDept->id);
        \App\Services\DashboardService::clearSuperAdminCache();

        $successMsg = $receiver
            ? 'File transferred to ' . $receiver->name . ' (' . $targetDept->name . ').'
            : 'File transferred to ' . $targetDept->name . '. No users assigned yet — file is held by the department.';

        return redirect()->route('files.index')->with('success', $successMsg);
    }
}
