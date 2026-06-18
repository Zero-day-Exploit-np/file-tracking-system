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

    public function approve($id)
    {
        $request = TransferRequest::findOrFail($id);
        $file = FileRecord::findOrFail($request->file_id);

        $fromUser = $file->current_user_id;
        $fromDept = $file->department_id;

        // update file
        $file->update([
            'current_user_id' => $request->to_user_id,
            'department_id' => $request->to_department,
        ]);

        // update request
        $request->update(['status' => 'approved']);

        // ✅ ADD TIMELINE ENTRY (THIS IS KEY)
        FileMovement::create([
            'file_id' => $file->id,
            'from_user' => $fromUser,
            'to_user' => $request->to_user_id,
            'from_department' => $fromDept,
            'to_department' => $request->to_department,
            'action' => 'approved',
            'remarks' => 'File transferred on approval'
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

        return response()->json([
            'success' => true,
            'message' => 'Rejected successfully'
        ]);
    }


    public function fileDetails($id)
    {
        $file = FileRecord::with([
            'currentUser',
            'department',
            'movements.fromUser',
            'movements.toUser',
            'movements.fromDept',
            'movements.toDept'
        ])->findOrFail($id);

        return view('admin.files.show', compact('file'));
    }
}
