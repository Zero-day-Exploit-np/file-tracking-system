<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = FileMovement::with([
            'file',
            'fromUser',
            'toUser',
            'fromDept',
            'toDept',
        ])->latest();

        // Department scope for non-super-admin
        if ($user->role !== 'super_admin') {
            $query->where(function ($q) use ($user) {
                $q->where('from_department', $user->department_id)
                  ->orWhere('to_department', $user->department_id);
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('file_number')) {
            $query->whereHas('file', function ($q) use ($request) {
                $q->where('file_number', 'like', '%' . $request->file_number . '%');
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(25);

        return view('admin.audit_logs.index', compact('logs'));
    }
}
