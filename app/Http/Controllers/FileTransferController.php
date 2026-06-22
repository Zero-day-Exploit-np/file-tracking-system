<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TransferRequest;
use App\Notifications\FileTransferredNotification;
use App\Events\FileTransferred;
use App\Models\FileMovement;

class FileTransferController extends Controller
{
    public function create($fileId)
    {
        $file = FileRecord::findOrFail($fileId);
        $currentUser = Auth::user();

        if ($currentUser->role !== 'super_admin' && $file->department_id !== $currentUser->department_id) {
            abort(403);
        }

        $users = User::where('id', '!=', auth()->id())
            ->whereNotNull('department_id')
            ->with(['department', 'designation'])
            ->get();

        return view('files.transfer', compact('file', 'users'));
    }

    // STORE TRANSFER
    public function store(Request $request)
    {
        $request->validate([
            'file_record_id' => 'required|exists:file_records,id',
            'to_user_id' => 'required|exists:users,id',
        ]);

        $file = FileRecord::findOrFail($request->file_record_id);
        $targetUser = User::findOrFail($request->to_user_id);

        $currentUser = Auth::user();

        if ($currentUser->role !== 'super_admin' && $file->department_id !== $currentUser->department_id) {
            abort(403);
        }

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'Please choose a different recipient for transfer.');
        }

        if (
            $currentUser->role !== 'super_admin' &&
            $targetUser->department_id != $currentUser->department_id
        ) {
            // Cross-department transfer — must go through approval workflow
            if (!$targetUser->department_id) {
                return back()->with('error', 'Target user has no department assigned.');
            }

            TransferRequest::create([
                'file_id' => $file->id,
                'requested_by' => $currentUser->id,
                'from_department' => $currentUser->department_id,
                'to_department' => $targetUser->department_id,
                'target_user' => $targetUser->id,
                'status' => 'pending'
            ]);

            FileMovement::create([
                'file_id' => $file->id,
                'from_user' => auth()->id(),
                'to_user' => $targetUser->id,
                'from_department' => auth()->user()->department_id,
                'to_department' => $targetUser->department_id,
                'action' => 'requested',
                'remarks' => 'Transfer request submitted'
            ]);

            $file->update([
                'status' => 'pending_transfer'
            ]);

            $this->recordAudit('requested', $file, [
                'file_number' => $file->file_number,
                'file_name' => $file->file_name,
                'from_user' => $currentUser->id,
                'to_user' => $targetUser->id,
                'from_department' => $currentUser->department_id,
                'to_department' => $targetUser->department_id,
                'remarks' => 'Transfer request submitted',
            ], 'Transfer request submitted');

            return back()->with(
                'success',
                'Transfer request sent for approval'
            );
        }

        $transfer = FileTransfer::create([
            'file_record_id' => $file->id,
            'from_user_id' => auth()->id(),
            'to_user_id' => $targetUser->id,
            'from_department_id' => auth()->user()->department_id,
            'to_department_id' => $targetUser->department_id,
            'remarks' => $request->remarks,
        ]);

        FileMovement::create([
            'file_id' => $file->id,
            'from_user' => auth()->id(),
            'to_user' => $targetUser->id,
            'from_department' => auth()->user()->department_id,
            'to_department' => $targetUser->department_id,
            'action' => 'transferred',
            'remarks' => $request->remarks ?? 'Same-department transfer',
        ]);

        $file->update([
            'current_user_id' => $targetUser->id,
            'department_id' => $targetUser->department_id,
            'status' => 'active',
        ]);

        $this->recordAudit('transferred', $file, [
            'file_number' => $file->file_number,
            'file_name' => $file->file_name,
            'from_user' => $currentUser->id,
            'to_user' => $targetUser->id,
            'from_department' => $currentUser->department_id,
            'to_department' => $targetUser->department_id,
            'remarks' => $request->remarks ?? 'Same-department transfer',
        ], 'File transferred');

        $targetUser->notify(new FileTransferredNotification($transfer));

        return redirect()
            ->route('files.index')
            ->with('success', 'File transferred successfully');
    }
}
