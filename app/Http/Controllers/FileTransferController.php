<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\TransferRequest;
use App\Models\User;
use App\Notifications\FileTransferredNotification;
use App\Notifications\TransferRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FileTransferController extends Controller
{
    public function create(FileRecord $file)
    {
        $currentUser = Auth::user();

        $this->authorize('transfer', $file);

        if ($file->status === 'pending_transfer') {
            return redirect()->route('files.show', $file->uuid)
                ->with('error', 'This file already has a pending transfer request.');
        }

        $users = User::where('id', '!=', Auth::id())
            ->whereNotNull('department_id')
            ->with(['department', 'designation'])
            ->orderBy('name')
            ->get();

        return view('files.transfer', compact('file', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_record_uuid' => 'required|string|exists:file_records,uuid',
            'to_user_id'       => 'required|integer|exists:users,id',
            'remarks'          => 'nullable|string|max:500',
        ]);

        $file        = FileRecord::where('uuid', $request->file_record_uuid)->firstOrFail();
        $targetUser  = User::findOrFail((int) $request->to_user_id);
        $currentUser = Auth::user();

        // IDOR check
        if (
            $currentUser->role !== 'super_admin' &&
            (int) $file->department_id !== (int) $currentUser->department_id
        ) {
            abort(403, 'You do not have access to transfer this file.');
        }

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'You cannot transfer a file to yourself.');
        }

        if ($file->status === 'pending_transfer') {
            return redirect()->route('files.show', $file->uuid)
                ->with('error', 'This file already has a pending transfer request.');
        }

        $remarks = $request->string('remarks')->trim()->value() ?: null;

        // ── SAME-DEPARTMENT: direct transfer, no approval needed ──────────
        $isSameDept = (int) $targetUser->department_id === (int) $currentUser->department_id;

        if ($isSameDept || $currentUser->role === 'super_admin') {
            $transfer = null;

            DB::transaction(function () use ($file, $currentUser, $targetUser, $remarks, $request, &$transfer) {
                $transfer = FileTransfer::create([
                    'file_id'       => $file->id,
                    'sender_id'     => $currentUser->id,
                    'receiver_id'   => $targetUser->id,
                    'remarks'       => $remarks,
                    'transferred_at' => now(),
                ]);

                FileMovement::create([
                    'file_id'         => $file->id,
                    'from_user'       => $currentUser->id,
                    'to_user'         => $targetUser->id,
                    'from_department' => $currentUser->department_id,
                    'to_department'   => $targetUser->department_id,
                    'action'          => 'transferred',
                    'remarks'         => $remarks ?? 'Same-department direct transfer',
                ]);

                $file->update([
                    'current_user_id' => $targetUser->id,
                    'status'          => 'active',
                ]);

                $this->recordAudit('file_transferred', $file, [
                    'from_user'      => $currentUser->id,
                    'to_user'        => $targetUser->id,
                    'from_department' => $currentUser->department_id,
                    'to_department'  => $targetUser->department_id,
                    'ip'             => $request->ip(),
                ], 'Direct transfer by ' . $currentUser->name);
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

        // ── CROSS-DEPARTMENT: requires SOURCE department admin approval ────
        if (!$targetUser->department_id) {
            return back()->with('error', 'Target user has no department assigned.');
        }

        // SRS FR8: notification goes to SOURCE department admin
        $transferReq = null;

        DB::transaction(function () use ($file, $currentUser, $targetUser, $remarks, $request, &$transferReq) {
            $transferReq = TransferRequest::create([
                'file_id'         => $file->id,
                'requested_by'    => $currentUser->id,
                'from_department' => $currentUser->department_id,  // source
                'to_department'   => $targetUser->department_id,   // destination
                'target_user'     => $targetUser->id,
                'status'          => 'pending',
            ]);

            FileMovement::create([
                'file_id'         => $file->id,
                'from_user'       => $currentUser->id,
                'to_user'         => $targetUser->id,
                'from_department' => $currentUser->department_id,
                'to_department'   => $targetUser->department_id,
                'action'          => 'requested',
                'remarks'         => $remarks ?? 'Cross-department transfer request submitted',
            ]);

            $file->update(['status' => 'pending_transfer']);

            $this->recordAudit('transfer_requested', $file, [
                'from_user'       => $currentUser->id,
                'to_user'         => $targetUser->id,
                'from_department' => $currentUser->department_id,
                'to_department'   => $targetUser->department_id,
                'ip'              => $request->ip(),
            ], 'Transfer request submitted by ' . $currentUser->name);
        });

        // Notify the SOURCE department admin (correct per SRS FR8)
        $sourceAdmins = User::where('department_id', $currentUser->department_id)
            ->where('role', 'admin')
            ->get();

        if ($transferReq) {
            foreach ($sourceAdmins as $sourceAdmin) {
                $sourceAdmin->notify(new TransferRequestNotification($transferReq));
            }
        }

        // Invalidate source admin's cache
        \App\Services\DashboardService::clearAdminCache($currentUser->department_id);
        \App\Services\DashboardService::clearSuperAdminCache();

        return back()->with(
            'success',
            'Transfer request submitted. Awaiting approval from your department admin (' .
                ($currentUser->department->name ?? 'your department') . ').'
        );
    }
}
