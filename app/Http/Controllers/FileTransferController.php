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
     * Any user who is the current holder may transfer — ownership-based, not role-based.
     */
    public function create(FileRecord $file)
    {
        $this->authorize('transfer', $file);

        $currentUser = Auth::user();

        // All active users in the same department (excluding self)
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

        // Auth check — policy enforces current holder (ownership-based, not role-based)
        $this->authorize('transfer', $file);

        // SECURITY: re-verify ownership hasn't changed since the form was loaded (race condition guard)
        if ((int) $file->current_user_id !== $currentUser->id) {
            return back()->with('error', 'You no longer hold this file.');
        }

        $remarks = $request->string('remarks')->trim()->value() ?: null;

        if ($request->destination_type === 'same') {
            $toUserId = (int) $request->to_user_id;

            // ── SECURITY: verify target user is actually in the same department ──
            $targetUser = User::where('id', $toUserId)
                ->where('department_id', $currentUser->department_id)
                ->where('is_active', true)
                ->first();

            if (!$targetUser) {
                return back()->with('error', 'Invalid recipient. The selected user must be in your department.');
            }

            return $this->transferToUser($file, $currentUser, $targetUser, $remarks);
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

    private function transferToUser(FileRecord $file, User $currentUser, User $targetUser, ?string $remarks): \Illuminate\Http\RedirectResponse
    {
        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'You cannot transfer a file to yourself.');
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

        // Cannot transfer to own department via "other dept" path
        if ((int) $targetDept->id === (int) $currentUser->department_id) {
            return back()->with('error', 'That is your own department. Use "Same Department" instead.');
        }

        // Find best receiver: prefer the dept admin, then any active user in that dept.
        // Per spec: cross-dept transfer owner = Department Admin.
        // If no admin AND no active user — block the transfer with a clear error.
        $receiver = User::where('department_id', $targetDept->id)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->first()
            ?? User::where('department_id', $targetDept->id)
                ->where('is_active', true)
                ->first();

        // RULE: Never save current_holder = null. If no one in target dept, block transfer.
        if (!$receiver) {
            return back()->with('error',
                'Cannot transfer to "' . $targetDept->name . '" — that department has no active users. ' .
                'Ask a Super Admin to assign users to that department first.'
            );
        }

        $transfer = null;

        DB::transaction(function () use ($file, $currentUser, $targetDept, $receiver, $remarks, &$transfer) {
            $transfer = FileTransfer::create([
                'file_id'        => $file->id,
                'sender_id'      => $currentUser->id,
                'receiver_id'    => $receiver->id,
                'remarks'        => $remarks,
                'transferred_at' => now(),
            ]);

            FileMovement::create([
                'file_id'         => $file->id,
                'from_user'       => $currentUser->id,
                'to_user'         => $receiver->id,
                'from_department' => $currentUser->department_id,
                'to_department'   => $targetDept->id,
                'action'          => 'transferred',
                'remarks'         => $remarks ?? 'Cross-department transfer to ' . $targetDept->name,
            ]);

            $file->update([
                'current_user_id' => $receiver->id,
                'department_id'   => $targetDept->id,
                'status'          => 'active',
            ]);
        });

        if ($transfer) {
            // Notify the direct receiver
            $receiver->notify(new FileTransferredNotification($transfer));

            // Also notify the dept admin if the receiver is not already the admin
            if ($receiver->role !== 'admin') {
                $deptAdmin = User::where('department_id', $targetDept->id)
                    ->where('role', 'admin')
                    ->where('is_active', true)
                    ->first();
                if ($deptAdmin && $deptAdmin->id !== $receiver->id) {
                    $deptAdmin->notify(new FileTransferredNotification($transfer));
                }
            }

            // Only broadcast when receiver_id is set (already guaranteed above)
            event(new \App\Events\FileTransferred($transfer));
        }

        \App\Services\DashboardService::clearUserCache($currentUser->id);
        \App\Services\DashboardService::clearUserCache($receiver->id);
        \App\Services\DashboardService::clearAdminCache($currentUser->department_id);
        \App\Services\DashboardService::clearAdminCache($targetDept->id);
        \App\Services\DashboardService::clearSuperAdminCache();

        return redirect()->route('files.index')
            ->with('success', 'File transferred to ' . $receiver->name . ' (' . $targetDept->name . ').');
    }
}
