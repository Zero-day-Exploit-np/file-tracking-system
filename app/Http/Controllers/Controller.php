<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function recordAudit(string $action, $auditable, array $metadata = [], ?string $description = null)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => is_object($auditable) ? get_class($auditable) : null,
            'auditable_id' => is_object($auditable) ? $auditable->getKey() : $auditable,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
