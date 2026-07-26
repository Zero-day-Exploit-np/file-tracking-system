<?php

namespace App\Http\Controllers;

use App\Models\FileRecord;
use Illuminate\Http\Request;

class PublicFileSearchController extends Controller
{
    /** Show the public file search page. */
    public function index()
    {
        return view('public.file-search');
    }

    /**
     * Search for a file by file number.
     * Returns only safe public fields — no internal user data.
     * The file journey is collapsed to department-level milestones.
     */
    public function search(Request $request)
    {
        $request->validate([
            'file_number' => 'required|string|max:100',
        ]);

        $fileNumber = trim($request->string('file_number')->value());

        $file = FileRecord::where('file_number', $fileNumber)
            ->with([
                'department',
                'currentHolder',
                'movements' => fn($q) => $q->with(['fromDept', 'toDept'])->orderBy('created_at'),
            ])
            ->first();

        if (!$file) {
            return back()
                ->withInput()
                ->with('search_error', 'No file found with this File Number.');
        }

        $holder     = $file->currentHolder;
        $holderName = $holder ? $holder->name : 'N/A';

        // ── Safe public file summary ─────────────────────────────────────
        $result = [
            'file_number'    => $file->file_number,
            'file_name'      => $file->file_name,
            'department'     => $file->department->name ?? 'N/A',
            'current_holder' => $holderName,
            'status'         => ucwords(str_replace('_', ' ', $file->status)),
            'created_date'   => $file->created_at->format('d M Y'),
        ];

        // ── Build public department-level journey ────────────────────────
        // Rules:
        //  1. Collapse consecutive movements in the same department into ONE node.
        //  2. A new node is created whenever the department changes.
        //  3. Never expose: user names, employee IDs, emails, internal usernames.
        //  4. Remarks are safe to show (they are file-level notes, not user-internal data).
        //     We use the last remark recorded while the file was in that department.
        $journey = $this->buildPublicJourney($file->movements);

        return view('public.file-search', compact('result', 'journey'))->with('searched', true);
    }

    /**
     * Collapse raw FileMovement records into department-level milestones.
     *
     * Each milestone contains:
     *   dept_name   — department name
     *   date        — date the file arrived in this dept (first movement into it)
     *   time        — time it arrived
     *   action      — 'Created' | 'Received' | 'Current'
     *   remark      — last non-null remarks recorded while in this dept
     *   is_current  — true for the last milestone (current department)
     */
    private function buildPublicJourney($movements): array
    {
        $journey      = [];
        $currentDept  = null;
        $currentDate  = null;
        $currentTime  = null;
        $lastRemark   = null;

        foreach ($movements as $move) {
            // Determine the relevant department for this movement
            if ($move->action === 'created') {
                $deptName = $move->fromDept?->name ?? 'Unknown Department';
                $date     = $move->created_at->format('d M Y');
                $time     = $move->created_at->format('h:i A');
                $action   = 'Created';
                $remark   = $move->remarks;
            } else {
                $deptName = $move->toDept?->name ?? 'Unknown Department';
                $date     = $move->created_at->format('d M Y');
                $time     = $move->created_at->format('h:i A');
                $action   = 'Received';
                $remark   = $move->remarks;
            }

            if ($deptName !== $currentDept) {
                // Department changed — push previous node if any
                if ($currentDept !== null) {
                    $journey[] = [
                        'dept_name'  => $currentDept,
                        'date'       => $currentDate,
                        'time'       => $currentTime,
                        'action'     => count($journey) === 0 ? 'Created' : 'Received',
                        'remark'     => $lastRemark,
                        'is_current' => false,
                    ];
                }

                $currentDept = $deptName;
                $currentDate = $date;
                $currentTime = $time;
                $lastRemark  = $remark ?: null;
            } else {
                // Still in same dept — update remark if this one is more informative
                if ($remark) {
                    $lastRemark = $remark;
                }
            }
        }

        // Push the final (current) dept node
        if ($currentDept !== null) {
            $journey[] = [
                'dept_name'  => $currentDept,
                'date'       => $currentDate,
                'time'       => $currentTime,
                'action'     => count($journey) === 0 ? 'Created' : 'Current',
                'remark'     => $lastRemark,
                'is_current' => true,
            ];
        }

        return $journey;
    }
}
