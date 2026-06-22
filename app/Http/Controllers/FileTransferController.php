<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TransferRequest;
use App\Notifications\FileTransferredNotification;
use App\Models\FileMovement;

class FileTransferController extends Controller
{
    public function create($fileId)
    {
        $file        = FileRecord::findOrFail($fileId);
        $currentUser = Auth::user();

        // Authorization: only the file's department members (or super admin) can initiate
        if ($currentUser->role !== 'super_admin' &&
            (int) $file->department_id !== (int) $currentUser->department_id) {
            abort(403, 'You do not have access to transfer this file.');
        }

        // File must not already be pending transfer
        if ($file->status === 'pending_transfer') {
            return back()->with('error', 'This file already has a pending transfer request.');
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
            'file_record_id' => 'required|exists:file_records,id',
            'to_user_id'     => 'required|exists:users,id',
            'remarks'        => 'nullable|string|max:500',
        ]);

        $file        = FileRecord::findOrFail((int) $request->file_record_id);
        $targetUser  = User::findOrFail((int) $request->to_user_id);
        $currentUser = Auth::user();

        // Re-verify authorization (IDOR prevention)
        if ($currentUser->role !== 'super_admin' &&
            (int) $file->department_id !== (int) $currentUser->department_id) {
            abort(403, 'You do not have access to transfer this file.');
        }

        // Cannot transfer to yourself
        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'You cannot transfer a file to yourself.');
        }

        // Cannot transfer a file already pending approval
        if ($file->status === 'pending_transfer') {
            return back()->with('error', 'This file already has a pending transfer request.');
        }

        $remarks = $request->string('remarks')->trim()->value() ?: null;

        /*
         * Cross-department → create a TransferRequest pending admin approval.
         * Super admin bypasses this and transfers directly.
         */
        $isCrossDept = $currentUser->role !== 'super_admin' &&
                       (int) $targetUser->department_id !== (int) $currentUser->department_id;

        if ($isCrossDept) {
            if (!$targetUser->department_id) {
                return back()->with('error', 'Target user has no department assigned.');
            }

            TransferRequest::create([
                'file_id'          => $file->id,
                'requested_by'     => $currentUser->id,
                'from_department'  => $currentUser->department_id,
                'to_department'    => $targetUser->department_id,
                'target_user'      => $targetUser->id,
                'status'           => 'pending',
            ]);

            FileMovement::create([
                'file_id'         => $file->id,
                'from_user'       => $currentUser->id,
                'to_user'         => $targetUser->id,
                'from_department' => $currentUser->department_id,
                'to_department'   => $targetUser->department_id,
                'action'          => 'requested',
                'remarks'         => 'Cross-department transfer request submitted',
            ]);

            $file->update(['status' => 'pending_transfer']);

            $this->recordAudit('transfer_requested', $file, [
                'file_number'    => $file->file_number,
                'from_user'      => $currentUser->id,
                'to_user'        => $targetUser->id,
                'from_department'=> $currentUser->department_id,
                'to_department'  => $targetUser->department_id,
                'ip'             => $request->ip(),
            ], 'Transfer request submitted by ' . $currentUser->name);

            return back()->with('success', 'Transfer request sent to the destination department admin for approval.');
        }

        // Same-department direct transfer (or super admin)
        $transfer = FileTransfer::create([
            'file_record_id'     => $file->id,
            'from_user_id'       => $currentUser->id,
            'to_user_id'         => $targetUser->id,
            'from_department_id' => $currentUser->department_id,
            'to_department_id'   => $targetUser->department_id,
            'remarks'            => $remarks,
        ]);

        FileMovement::create([
            'file_id'         => $file->id,
            'from_user'       => $currentUser->id,
            'to_user'         => $targetUser->id,
            'from_department' => $currentUser->department_id,
            'to_department'   => $targetUser->department_id,
            'action'          => 'transferred',
            'remarks'         => $remarks ?? 'Direct transfer',
        ]);

        $file->update([
            'current_user_id' => $targetUser->id,
            'department_id'   => $targetUser->department_id,
            'status'          => 'active',
        ]);

        $this->recordAudit('file_transferred', $file, [
            'file_number'    => $file->file_number,
            'from_user'      => $currentUser->id,
            'to_user'        => $targetUser->id,
            'from_department'=> $currentUser->department_id,
            'to_department'  => $targetUser->department_id,
            'ip'             => $request->ip(),
        ], 'File transferred by ' . $currentUser->name);

        $targetUser->notify(new FileTransferredNotification($transfer));

        return redirect()->route('files.index')->with('success', 'File transferred successfully.');
    }
}
