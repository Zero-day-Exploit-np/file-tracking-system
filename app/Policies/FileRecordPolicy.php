<?php

namespace App\Policies;

use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;

class FileRecordPolicy
{
    /**
     * Super Admin and Admin: can view files but CANNOT create files.
     * Only role === 'user' may create files (with can_create_file flag).
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super Admin: can view/download but NOT create
        if ($user->role === 'super_admin') {
            if (in_array($ability, ['create', 'store'], true)) {
                return false;
            }
            return true;
        }

        // Admin: can view dept files but NOT create
        if ($user->role === 'admin') {
            if (in_array($ability, ['create', 'store'], true)) {
                return false;
            }
        }

        return null;
    }

    /**
     * View a file:
     * - creator
     * - current holder
     * - same-department admin
     * - anyone who appeared in transfer history (sent or received)
     */
    public function view(User $user, FileRecord $file): bool
    {
        return $this->hasFileAccess($user, $file);
    }

    /**
     * Download: same as view.
     */
    public function download(User $user, FileRecord $file): bool
    {
        return $this->hasFileAccess($user, $file);
    }

    public function update(User $user, FileRecord $file): bool
    {
        return $this->hasFileAccess($user, $file);
    }

    /**
     * Transfer: must be the current holder.
     * Only role:user may transfer (admins/super_admins cannot).
     * Archived files cannot be transferred.
     */
    public function transfer(User $user, FileRecord $file): bool
    {
        if ($user->role !== 'user') {
            return false;
        }

        if ($file->status === 'archived') {
            return false;
        }

        return (int) $file->current_user_id === $user->id;
    }

    /**
     * Create: ONLY role === 'user' with can_create_file flag.
     * Admin and Super Admin are BLOCKED via before().
     */
    public function create(User $user): bool
    {
        return $user->role === 'user' && (bool) $user->can_create_file;
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    /**
     * A user has access if they are:
     * 1. The creator
     * 2. The current holder
     * 3. A same-department admin (read-only)
     * 4. Previously involved as sender OR receiver in file_transfers
     */
    private function hasFileAccess(User $user, FileRecord $file): bool
    {
        // 1. Creator
        if ((int) $file->created_by === $user->id) return true;

        // 2. Current holder
        if ((int) $file->current_user_id === $user->id) return true;

        // 3. Same-department admin (read-only view)
        if ($user->role === 'admin' && (int) $user->department_id === (int) $file->department_id) {
            return true;
        }

        // 4. Was involved in a transfer for this file
        return FileTransfer::where('file_id', $file->id)
            ->where(fn($q) => $q
                ->where('sender_id',   $user->id)
                ->orWhere('receiver_id', $user->id))
            ->exists();
    }
}
