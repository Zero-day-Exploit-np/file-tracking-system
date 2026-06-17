<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FileTransferController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file_record_id' => 'required|exists:file_records,id',
            'to_user_id' => 'required|exists:users,id',
        ]);

        $file = FileRecord::findOrFail($request->file_record_id);

        // Save transfer history
        FileTransfer::create([
            'file_record_id' => $file->id,
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'remarks' => $request->remarks,
        ]);

        // Update current holder
        $file->update([
            'current_user_id' => $request->to_user_id,
        ]);

        return back()->with('success', 'File transferred successfully');
    }
}
