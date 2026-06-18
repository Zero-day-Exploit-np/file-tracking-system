<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use App\Models\FileRecord;

class TransferApprovalController extends Controller
{
    //
    public function index()
    {
        $requests = TransferRequest::where(
            'to_department',
            auth()->user()->department_id
        )->latest()->get();

        return view(
            'admin.transfer_requests.index',
            compact('requests')
        );
    }
    public function approve($id)
    {
        $request = TransferRequest::findOrFail($id);

        $file = FileRecord::findOrFail($request->file_id);

        $file->current_holder =
            $request->target_user;

        $file->department_id =
            $request->to_department;

        $file->save();

        $request->update([
            'status' => 'approved'
        ]);

        return back();
    }
    public function reject($id)
    {
        TransferRequest::findOrFail($id)
            ->update([
                'status' => 'rejected'
            ]);

        return back();
    }
}
