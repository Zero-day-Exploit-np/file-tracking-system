<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TransferApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $relations = ['file', 'sender', 'receiver', 'fromDept', 'toDept'];

        $query = TransferRequest::with($relations)->latest();

        if ($user->role !== 'super_admin') {
            $query->where('to_department', $user->department_id);
        }

        $pending = (clone $query)->where('status', 'pending')->get();
        $approved = (clone $query)->where('status', 'approved')->get();
        $rejected = (clone $query)->where('status', 'rejected')->get();

        return view('admin.transfer_requests.index', compact(
            'pending',
            'approved',
            'rejected'
        ));
    }

    public function approve($id)
    {
        $request = TransferRequest::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'super_admin' && $request->to_department !== $user->department_id) {
            abort(403);
        }

        $file = FileRecord::findOrFail($request->file_id);
        $targetUser = User::findOrFail($request->target_user);

        $fromUser = $file->current_user_id;
        $fromDept = $file->department_id;

        FileTransfer::create([
            'file_record_id' => $file->id,
            'from_user_id' => $request->requested_by,
            'to_user_id' => $request->target_user,
            'from_department_id' => $request->from_department,
            'to_department_id' => $request->to_department,
            'remarks' => 'Approved by Admin'
        ]);

        FileMovement::create([
            'file_id' => $file->id,
            'from_user' => $fromUser,
            'to_user' => $targetUser->id,
            'from_department' => $fromDept,
            'to_department' => $request->to_department,
            'action' => 'approved',
            'remarks' => 'File transferred after admin approval'
        ]);

        $file->update([
            'current_user_id' => $request->target_user,
            'department_id' => $request->to_department,
            'status' => 'active'
        ]);

        $request->update([
            'status' => 'approved'
        ]);

        $this->recordAudit('approved', $file, [
            'file_number' => $file->file_number,
            'file_name' => $file->file_name,
            'requested_by' => $request->requested_by,
            'from_user' => $fromUser,
            'to_user' => $request->target_user,
            'from_department' => $fromDept,
            'to_department' => $request->to_department,
            'remarks' => $request->remarks,
        ], 'Transfer request approved');

        return response()->json([
            'success' => true,
            'message' => 'Transfer Approved'
        ]);
    }

    public function reject($id)
    {
        $request = TransferRequest::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'super_admin' && $request->to_department !== $user->department_id) {
            abort(403);
        }

        $file = FileRecord::findOrFail($request->file_id);

        $request->update(['status' => 'rejected']);

        FileMovement::create([
            'file_id' => $file->id,
            'from_user' => $file->current_user_id,
            'to_user' => null,
            'from_department' => $file->department_id,
            'to_department' => $request->to_department,
            'action' => 'rejected',
            'remarks' => 'Transfer rejected'
        ]);
        $file->update([
            'status' => 'active'
        ]);

        $this->recordAudit('rejected', $file, [
            'file_number' => $file->file_number,
            'file_name' => $file->file_name,
            'from_user' => $file->current_user_id,
            'to_user' => $request->target_user,
            'from_department' => $file->department_id,
            'to_department' => $request->to_department,
            'remarks' => 'Transfer rejected',
        ], 'Transfer request rejected');

        return response()->json([
            'success' => true,
            'message' => 'Rejected successfully'
        ]);
    }


    // fileDetails handled by FileTimelineController
}

