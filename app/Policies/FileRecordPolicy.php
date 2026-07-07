<?php

namespace App\Policies;

use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;

class FileRecordPolicy
{
    /**
     * before() — short-circuit checks before specific ability methods.
     *
     * Super Admin: can do everything except create files (no restriction on dept).
     * Admin:       can view dept files, cannot create.
     * Transfer:    always bypasses before() — handled by transfer() method.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Transfer is ownership-based — bypass before() entirely
        if ($ability === 'transfer') {
            return null;
        }

        // Super Admin: can view/download/update but NOT create files
        if ($user->role === 'super_admin') {
            if (in_array($ability, ['create', 'store'], true)) {
                return false;
            }
            return true;
        }

        return null;
    }

    /**
     * View a file:
     * - creator
     * - current holder
     * - same-department admin (read-only)
     * - anyone who appeared in transfer history
     */
    public function view(User $user, FileRecord $file): bool
    {
        return $this->hasFileAccess($user, $file);
    }

    /** Download: same access rules as view. */
    public function download(User $user, FileRecord $file): bool
    {
        return $this->hasFileAccess($user, $file);
    }

    public function update(User $user, FileRecord $file): bool
    {
        return $this->hasFileAccess($user, $file);
    }

    /**
     * Transfer: ownership-based, not role-based.
     * Whoever currently holds the file can transfer it.
     * Archived files cannot be transferred.
     */
    public function transfer(User $user, FileRecord $file): bool
    {
        if ($file->status === 'archived') {
            return false;
        }
        return (int) $file->current_user_id === $user->id;
    }

    /**
     * Create: any authenticated user with can_create_file = true.
     * They may choose any department (restriction removed per spec).
     * Super Admin is blocked via before().
     */
    public function create(User $user): bool
    {
        return (bool) $user->can_create_file;
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    private function hasFileAccess(User $user, FileRecord $file): bool
    {
        // Creator
        if ((int) $file->created_by === $user->id) return true;

        // Current holder
        if ((int) $file->current_user_id === $user->id) return true;

        // Same-department admin (read-only view)
        if ($user->role === 'admin' && (int) $user->department_id === (int) $file->department_id) {
            return true;
        }

        // Was involved in a transfer for this file
        return FileTransfer::where('file_id', $file->id)
            ->where(fn($q) => $q
                ->where('sender_id',   $user->id)
                ->orWhere('receiver_id', $user->id))
            ->exists();
    }
}
