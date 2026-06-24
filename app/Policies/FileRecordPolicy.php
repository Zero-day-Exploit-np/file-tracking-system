<?php

namespace App\Policies;

use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;

class FileRecordPolicy
{
    /**
     * Super Admin: can view/download/transfer but CANNOT create files.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'super_admin') {
            // Block create/store
            if (in_array($ability, ['create', 'store'], true)) {
                return false;
            }
            // Allow all other abilities
            return true;
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

    /**
     * Transfer: must be current holder OR same-department admin.
     * Files with pending_transfer status cannot be transferred again.
     */
    public function transfer(User $user, FileRecord $file): bool
    {
        // Never allow transfer on a file already pending
        if ($file->status === 'pending_transfer') {
            return false;
        }

        // Same-department admin can initiate transfer
        if ($user->role === 'admin' && (int) $user->department_id === (int) $file->department_id) {
            return true;
        }

        // Only current holder may transfer
        return (int) $file->current_user_id === $user->id;
    }

    /**
     * Create: admins and users granted can_create_file.
     * Super Admin is explicitly BLOCKED via before().
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin' || (bool) $user->can_create_file;
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    /**
     * A user has access if they are:
     * 1. The creator
     * 2. The current holder
     * 3. A same-department admin
     * 4. Previously involved as sender OR receiver in file_transfers
     */
    private function hasFileAccess(User $user, FileRecord $file): bool
    {
        // 1. Creator
        if ((int) $file->created_by === $user->id) return true;

        // 2. Current holder
        if ((int) $file->current_user_id === $user->id) return true;

        // 3. Same-department admin
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
