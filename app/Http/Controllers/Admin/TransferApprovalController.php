<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\TransferRequest;
use App\Models\User;
use App\Notifications\FileTransferredNotification;
use App\Notifications\TransferStatusNotification;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferApprovalController extends Controller
{
    /**
     * List transfer requests.
     * SRS FR8: Admin sees requests where THEIR department is the SOURCE (from_department).
     * Super Admin sees all (read-only).
     */
    public function index()
    {
        $user    = Auth::user();
        $isSuper = $user->role === 'super_admin';

        $query = TransferRequest::with(['file', 'sender', 'receiver', 'fromDept', 'toDept'])->latest();

        if (!$isSuper) {
            // SOURCE department admin sees requests initiated by their department
            $query->where('from_department', $user->department_id);
        }

        $pending  = (clone $query)->where('status', 'pending')->get();
        $approved = (clone $query)->where('status', 'approved')->get();
        $rejected = (clone $query)->where('status', 'rejected')->get();

        return view('admin.transfer_requests.index', compact('pending', 'approved', 'rejected', 'isSuper'));
    }

    /**
     * Approve a cross-department transfer request.
     * Only the SOURCE department admin can approve (SRS FR8).
     */
    public function approve(string $uuid)
    {
        try {
            $transferReq = TransferRequest::where('uuid', $uuid)->firstOrFail();
            $admin       = Auth::user();

            // SOURCE admin check (not destination)
            if ((int) $transferReq->from_department !== (int) $admin->department_id) {
                return response()->json(['success' => false, 'message' => 'Only the source department admin can approve this transfer.'], 403);
            }

            if ($transferReq->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'This request is no longer pending.'], 422);
            }

            $file       = FileRecord::findOrFail($transferReq->file_id);
            $targetUser = User::findOrFail($transferReq->target_user);
            $requester  = User::findOrFail($transferReq->requested_by);
            $fromUser   = $file->current_user_id;
            $fromDept   = $file->department_id;

            $transfer = null;

            DB::transaction(function () use ($transferReq, $file, $targetUser, $requester, $admin, $fromUser, $fromDept, &$transfer) {

                // 1. Create transfer record
                $transfer = FileTransfer::create([
                    'file_id'        => $file->id,
                    'sender_id'      => $transferReq->requested_by,
                    'receiver_id'    => $transferReq->target_user,
                    'remarks'        => 'Approved by ' . $admin->name,
                    'transferred_at' => now(),
                ]);

                // 2. Record movement in timeline
                FileMovement::create([
                    'file_id'         => $file->id,
                    'from_user'       => $fromUser,
                    'to_user'         => $targetUser->id,
                    'from_department' => $fromDept,
                    'to_department'   => $transferReq->to_department,
                    'action'          => 'approved',
                    'remarks'         => 'Approved by source admin: ' . $admin->name,
                ]);

                // 3. Update file: move to target user and destination department
                $file->update([
                    'current_user_id' => $targetUser->id,
                    'department_id'   => $transferReq->to_department,
                    'status'          => 'active',
                ]);

                // 4. Mark request approved
                $transferReq->update(['status' => 'approved']);

                // 5. Audit log
                $this->recordAudit('transfer_approved', $file, [
                    'approved_by'     => $admin->id,
                    'from_user'       => $fromUser,
                    'to_user'         => $targetUser->id,
                    'from_department' => $fromDept,
                    'to_department'   => $transferReq->to_department,
                    'ip'              => request()->ip(),
                ], 'Transfer approved by ' . $admin->name);
            });

            if ($transfer) {
                // 6. Notify the target user (file receiver)
                $targetUser->notify(new FileTransferredNotification($transfer));

                // 7. Notify the requester that their request was approved
                $requester->notify(new TransferStatusNotification($transferReq, 'approved', $admin->name));
            }

            // Invalidate all relevant caches
            DashboardService::clearAdminCache($admin->department_id);
            DashboardService::clearAdminCache((int) $transferReq->to_department);
            DashboardService::clearSuperAdminCache();
            DashboardService::clearUserCache($targetUser->id);
            DashboardService::clearUserCache($requester->id);

            return response()->json([
                'success' => true,
                'message' => 'Transfer approved. File moved to ' . $targetUser->name . '.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Transfer approval failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Unable to approve transfer. Please try again.'], 500);
        }
    }

    /**
     * Reject a cross-department transfer request.
     * Only the SOURCE department admin can reject (SRS FR8).
     */
    public function reject(string $uuid)
    {
        try {
            $transferReq = TransferRequest::where('uuid', $uuid)->firstOrFail();
            $admin       = Auth::user();

            // SOURCE admin check
            if ((int) $transferReq->from_department !== (int) $admin->department_id) {
                return response()->json(['success' => false, 'message' => 'Only the source department admin can reject this transfer.'], 403);
            }

            if ($transferReq->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'This request is no longer pending.'], 422);
            }

            $file      = FileRecord::findOrFail($transferReq->file_id);
            $requester = User::findOrFail($transferReq->requested_by);

            DB::transaction(function () use ($transferReq, $file, $requester, $admin) {

                // 1. Mark rejected
                $transferReq->update(['status' => 'rejected']);

                // 2. Revert file status to active (file stays with current holder)
                $file->update(['status' => 'active']);

                // 3. Record rejection in timeline
                FileMovement::create([
                    'file_id'         => $file->id,
                    'from_user'       => $file->current_user_id,
                    'to_user'         => $transferReq->target_user,
                    'from_department' => $file->department_id,
                    'to_department'   => $transferReq->to_department,
                    'action'          => 'rejected',
                    'remarks'         => 'Transfer rejected by source admin: ' . $admin->name,
                ]);

                // 4. Audit log
                $this->recordAudit('transfer_rejected', $file, [
                    'rejected_by'     => $admin->id,
                    'from_department' => $file->department_id,
                    'to_department'   => $transferReq->to_department,
                    'ip'              => request()->ip(),
                ], 'Transfer rejected by ' . $admin->name);
            });

            $requester->notify(new TransferStatusNotification($transferReq, 'rejected', $admin->name));

            // Invalidate caches
            DashboardService::clearAdminCache($admin->department_id);
            DashboardService::clearSuperAdminCache();
            DashboardService::clearUserCache($requester->id);

            return response()->json([
                'success' => true,
                'message' => 'Transfer request rejected.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Transfer rejection failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Unable to reject transfer. Please try again.'], 500);
        }
    }
}
