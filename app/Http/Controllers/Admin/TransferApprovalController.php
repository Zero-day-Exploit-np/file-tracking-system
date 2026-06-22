<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferRequest;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TransferApprovalController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $isSuper   = $user->role === 'super_admin';
        $relations = ['file', 'sender', 'receiver', 'fromDept', 'toDept'];

        $query = TransferRequest::with($relations)->latest();

        // Admin only sees transfers destined for their department
        // Super admin sees ALL (read-only monitor view)
        if (!$isSuper) {
            $query->where('to_department', $user->department_id);
        }

        $pending  = (clone $query)->where('status', 'pending')->get();
        $approved = (clone $query)->where('status', 'approved')->get();
        $rejected = (clone $query)->where('status', 'rejected')->get();

        return view('admin.transfer_requests.index', compact(
            'pending', 'approved', 'rejected', 'isSuper'
        ));
    }

    /**
     * Approve a transfer request.
     * Middleware already enforces role:admin — super_admin cannot reach this.
     */
    public function approve($id)
    {
        $transferReq = TransferRequest::findOrFail($id);
        $admin       = Auth::user();

        // Double-check: only the destination department admin may approve
        if ($transferReq->to_department !== $admin->department_id) {
            abort(403, 'You can only approve transfers destined for your department.');
        }

        $file       = FileRecord::findOrFail($transferReq->file_id);
        $targetUser = User::findOrFail($transferReq->target_user);
        $fromUser   = $file->current_user_id;
        $fromDept   = $file->department_id;

        // Create transfer record
        FileTransfer::create([
            'file_record_id'     => $file->id,
            'from_user_id'       => $transferReq->requested_by,
            'to_user_id'         => $transferReq->target_user,
            'from_department_id' => $transferReq->from_department,
            'to_department_id'   => $transferReq->to_department,
            'remarks'            => 'Approved by ' . $admin->name,
        ]);

        // Log movement
        FileMovement::create([
            'file_id'         => $file->id,
            'from_user'       => $fromUser,
            'to_user'         => $targetUser->id,
            'from_department' => $fromDept,
            'to_department'   => $transferReq->to_department,
            'action'          => 'approved',
            'remarks'         => 'Transfer approved by department admin: ' . $admin->name,
        ]);

        // Update file ownership
        $file->update([
            'current_user_id' => $transferReq->target_user,
            'department_id'   => $transferReq->to_department,
            'status'          => 'active',
        ]);

        $transferReq->update(['status' => 'approved']);

        $this->recordAudit('approved', $file, [
            'file_number'     => $file->file_number,
            'file_name'       => $file->file_name,
            'approved_by'     => $admin->id,
            'from_user'       => $fromUser,
            'to_user'         => $transferReq->target_user,
            'from_department' => $fromDept,
            'to_department'   => $transferReq->to_department,
        ], 'Transfer approved by ' . $admin->name);

        return response()->json(['success' => true, 'message' => 'Transfer approved successfully.']);
    }

    /**
     * Reject a transfer request.
     * Middleware already enforces role:admin — super_admin cannot reach this.
     */
    public function reject($id)
    {
        $transferReq = TransferRequest::findOrFail($id);
        $admin       = Auth::user();

        // Double-check: only the destination department admin may reject
        if ($transferReq->to_department !== $admin->department_id) {
            abort(403, 'You can only reject transfers destined for your department.');
        }

        $file = FileRecord::findOrFail($transferReq->file_id);

        $transferReq->update(['status' => 'rejected']);

        FileMovement::create([
            'file_id'         => $file->id,
            'from_user'       => $file->current_user_id,
            'to_user'         => null,
            'from_department' => $file->department_id,
            'to_department'   => $transferReq->to_department,
            'action'          => 'rejected',
            'remarks'         => 'Transfer rejected by department admin: ' . $admin->name,
        ]);

        $file->update(['status' => 'active']);

        $this->recordAudit('rejected', $file, [
            'file_number'     => $file->file_number,
            'file_name'       => $file->file_name,
            'rejected_by'     => $admin->id,
            'from_department' => $file->department_id,
            'to_department'   => $transferReq->to_department,
        ], 'Transfer rejected by ' . $admin->name);

        return response()->json(['success' => true, 'message' => 'Transfer rejected.']);
    }
}
