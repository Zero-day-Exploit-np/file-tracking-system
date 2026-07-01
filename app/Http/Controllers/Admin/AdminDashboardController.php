<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferRequest;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function index()
    {
        $user    = Auth::user();
        $isSuper = $user->role === 'super_admin';

        if ($isSuper) {
            $stats  = $this->dashboard->superAdminStats();
            $audit  = $this->dashboard->superAdminAuditStats();
            $depts  = $this->dashboard->departmentFileCounts();
            $recent = $this->dashboard->superAdminRecentData();

            return view('super_admin.dashboard', [
                'totalFiles'           => $stats['total_files'],
                'totalDepartments'     => $stats['total_departments'],
                'totalUsers'           => $stats['total_users'],
                'pendingTransfers'     => $stats['pending_transfers'],
                'totalAdmins'          => $stats['total_admins'],
                'auditStats'           => $audit,
                'departmentFileCounts' => $depts,
                'recentTransfers'      => $recent['recentTransfers'],
                'recentAudit'          => $recent['recentAudit'],
                'pendingRequests'      => $recent['pendingRequests'],
            ]);
        }

        $deptId = $user->department_id;
        $stats  = $this->dashboard->adminStats($deptId);
        $recent = $this->dashboard->adminRecentData($deptId);

        return view('admin.dashboard', [
            'deptFiles'          => $stats['dept_files'],
            'deptUsers'          => $stats['dept_users'],
            'pendingRequests'    => $stats['pending_requests'],
            'completedTransfers' => $stats['completed_transfers'],
            'recentFiles'        => $recent['recentFiles'],
            'recentActivity'     => $recent['recentActivity'],
            'pendingApprovals'   => $recent['pendingApprovals'],
            'recentUsers'        => $recent['recentUsers'],
        ]);
    }

    /**
     * AJAX poll — returns fresh pending approval data for the admin dashboard.
     * Called every 10 seconds by the admin dashboard JavaScript.
     * Returns JSON with count + rows HTML for re-rendering.
     */
    public function poll()
    {
        $user   = Auth::user();
        $deptId = $user->department_id;

        if ($user->role !== 'admin') {
            return response()->json(['pending_count' => 0, 'rows' => '']);
        }

        // Always fresh — bypass cache for real-time accuracy
        $pendingApprovals = TransferRequest::with(['file', 'sender', 'receiver', 'fromDept'])
            ->where('status', 'pending')
            ->where('from_department', $deptId)
            ->latest()
            ->get();

        $count = $pendingApprovals->count();

        // Build HTML rows to inject into the table
        $rows = '';
        if ($count === 0) {
            $rows = '<tr><td colspan="5"><div class="empty-state py-3">'
                  . '<i class="fa-solid fa-check"></i>No pending approvals.</div></td></tr>';
        } else {
            foreach ($pendingApprovals as $req) {
                $uuid      = e($req->uuid);
                $fileName  = e($req->file->file_name ?? 'N/A');
                $fileNum   = e($req->file->file_number ?? '');
                $fromDept  = e($req->fromDept->name ?? 'N/A');
                $sender    = e($req->sender->name ?? 'N/A');
                $date      = e($req->created_at->format('d M Y'));
                $csrf      = csrf_token();

                $rows .= <<<HTML
<tr id="dash-row-{$uuid}">
  <td>
    <div class="fw-700">{$fileName}</div>
    <div class="text-muted fs-sm">{$fileNum}</div>
  </td>
  <td class="text-muted">{$fromDept}</td>
  <td>{$sender}</td>
  <td class="text-muted fs-sm">{$date}</td>
  <td>
    <div class="d-flex gap-1">
      <button onclick="dashAction('{$uuid}', 'approve', this)" class="btn btn-sm btn-success">
        <i class="fa-solid fa-check"></i>
      </button>
      <button onclick="dashAction('{$uuid}', 'reject', this)" class="btn btn-sm btn-danger">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  </td>
</tr>
HTML;
            }
        }

        return response()->json([
            'pending_count' => $count,
            'rows'          => $rows,
        ]);
    }
}
