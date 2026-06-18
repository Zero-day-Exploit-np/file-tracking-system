<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use App\Models\FileMovement;
use App\Models\FileRecord;

class TransferApprovalController extends Controller
{

    // public function index()
    // {
    //     $deptId = auth()->user()->department_id;

    //     $pending = TransferRequest::with('file')
    //         ->where('to_department', $deptId)
    //         ->where('status', 'pending')
    //         ->latest()
    //         ->get();

    //     $approved = TransferRequest::with('file')
    //         ->where('to_department', $deptId)
    //         ->where('status', 'approved')
    //         ->latest()
    //         ->get();

    //     $rejected = TransferRequest::with('file')
    //         ->where('to_department', $deptId)
    //         ->where('status', 'rejected')
    //         ->latest()
    //         ->get();

    //     return view('admin.transfer_requests.index', compact(
    //         'pending',
    //         'approved',
    //         'rejected'
    //     ));
    // }


    public function index()
    {
        $deptId = auth()->user()->department_id;

        $relations = ['file', 'sender', 'receiver', 'fromDept', 'toDept'];

        $pending = TransferRequest::with($relations)
            ->where('to_department', $deptId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $approved = TransferRequest::with($relations)
            ->where('to_department', $deptId)
            ->where('status', 'approved')
            ->latest()
            ->get();

        $rejected = TransferRequest::with($relations)
            ->where('to_department', $deptId)
            ->where('status', 'rejected')
            ->latest()
            ->get();

        return view('admin.transfer_requests.index', compact(
            'pending',
            'approved',
            'rejected'
        ));
    }

    // public function approve($id)
    // {
    //     $request = TransferRequest::findOrFail($id);

    //     $file = FileRecord::findOrFail($request->file_id);

    //     $fromUser = $file->current_user_id;
    //     $fromDept = $file->department_id;


    //     $file->update([
    //         'current_user_id' => $request->target_user,
    //         'department_id' => $request->to_department,
    //         'current_holder' => $request->target_user // if used
    //     ]);

    //     $request->update(['status' => 'approved']);

    //     FileMovement::create([
    //         'file_id' => $request->file_id,
    //         'from_user' => $fromUser,
    //         'to_user' => $request->target_user,
    //         'from_department' => $fromDept,
    //         'to_department' => $request->to_department,
    //         'action' => 'rejected',
    //         'remarks' => 'Request rejected'
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Approved successfully'
    //     ]);
    // }

    // public function reject($id)
    // {
    //     $request = TransferRequest::findOrFail($id);

    //     $request->update(['status' => 'rejected']);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Request rejected',
    //         'id' => $request->id
    //     ]);
    // }



    public function approve($id)
    {
        $request = TransferRequest::findOrFail($id);

        $file = FileRecord::findOrFail($request->file_id);

        $fromUser = $file->current_user_id;
        $fromDept = $file->department_id;

        $file->update([
            'current_user_id' => $request->target_user,
            'department_id' => $request->to_department,
            'current_holder' => $request->target_user
        ]);

        $request->update(['status' => 'approved']);

        FileMovement::create([
            'file_id' => $request->file_id,
            'from_user' => $fromUser,
            'to_user' => $request->target_user,
            'from_department' => $fromDept,
            'to_department' => $request->to_department,
            'action' => 'approved', // ✅ FIXED
            'remarks' => 'Request approved'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Approved successfully'
        ]);
    }
    public function reject($id)
    {
        $request = TransferRequest::findOrFail($id);

        $file = FileRecord::findOrFail($request->file_id);

        FileMovement::create([
            'file_id' => $file->id,
            'from_user' => null,
            'to_user' => $file->created_by,
            'from_department' => null,
            'to_department' => $file->department_id,
            'action' => 'created',
            'remarks' => 'File created'
        ]);

        $request->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected',
            'id' => $request->id
        ]);
    }
}
