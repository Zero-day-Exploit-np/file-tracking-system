<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    private const BACKUP_DISK = 'local'; // storage/app/private
    private const BACKUP_DIR  = 'backups';

    public function __construct()
    {
        // Only super_admin may access backup routes
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'super_admin') {
                abort(403, 'Only Super Admin may access the backup module.');
            }
            return $next($request);
        });
    }

    /**
     * Show backup history.
     */
    public function index()
    {
        $backups = $this->getBackupList();
        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Create a new database backup.
     */
    public function create(Request $request)
    {
        try {
            $timestamp  = now()->format('Y-m-d_H-i-s');
            $filename   = "backup_db_{$timestamp}.sql";
            $backupPath = self::BACKUP_DIR . '/' . $filename;

            $sql = $this->dumpDatabase();

            Storage::disk(self::BACKUP_DISK)->put($backupPath, $sql);

            AuditLog::create([
                'user_id'        => Auth::id(),
                'action'         => 'backup_created',
                'auditable_type' => 'system',
                'auditable_id'   => 0,
                'description'    => "Database backup created: {$filename}",
                'metadata'       => [
                    'filename' => $filename,
                    'size'     => strlen($sql),
                    'ip'       => $request->ip(),
                ],
            ]);

            Log::info('Backup created', ['filename' => $filename, 'user' => Auth::id()]);

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup created: {$filename}");

        } catch (\Throwable $e) {
            Log::error('Backup creation failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(string $filename)
    {
        // Sanitise filename — prevent path traversal
        $filename = basename($filename);
        $path     = self::BACKUP_DIR . '/' . $filename;

        if (!Storage::disk(self::BACKUP_DISK)->exists($path)) {
            abort(404, 'Backup file not found.');
        }

        AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => 'backup_downloaded',
            'auditable_type' => 'system',
            'auditable_id'   => 0,
            'description'    => "Backup downloaded: {$filename}",
            'metadata'       => ['filename' => $filename, 'ip' => request()->ip()],
        ]);

        return Storage::disk(self::BACKUP_DISK)->download($path, $filename);
    }

    /**
     * Delete a backup file.
     */
    public function destroy(Request $request, string $filename)
    {
        $filename = basename($filename);
        $path     = self::BACKUP_DIR . '/' . $filename;

        if (Storage::disk(self::BACKUP_DISK)->exists($path)) {
            Storage::disk(self::BACKUP_DISK)->delete($path);

            AuditLog::create([
                'user_id'        => Auth::id(),
                'action'         => 'backup_deleted',
                'auditable_type' => 'system',
                'auditable_id'   => 0,
                'description'    => "Backup deleted: {$filename}",
                'metadata'       => ['filename' => $filename, 'ip' => $request->ip()],
            ]);
        }

        return redirect()->route('admin.backup.index')
            ->with('success', "Backup deleted: {$filename}");
    }

    /**
     * Get list of existing backups with metadata.
     */
    private function getBackupList(): array
    {
        $disk  = Storage::disk(self::BACKUP_DISK);
        $files = $disk->exists(self::BACKUP_DIR)
            ? $disk->files(self::BACKUP_DIR)
            : [];

        $backups = [];
        foreach ($files as $file) {
            $filename  = basename($file);
            $backups[] = [
                'filename'     => $filename,
                'size'         => $this->formatBytes($disk->size($file)),
                'size_bytes'   => $disk->size($file),
                'created_at'   => \Carbon\Carbon::createFromTimestamp($disk->lastModified($file)),
                'type'         => Str::contains($filename, '_db_') ? 'Database' : 'Files',
                'download_url' => route('admin.backup.download', $filename),
                'delete_url'   => route('admin.backup.destroy', $filename),
            ];
        }

        // Sort newest first
        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    /**
     * Dump the entire database to SQL using PDO.
     * Works without mysqldump binary.
     */
    private function dumpDatabase(): string
    {
        $pdo    = DB::connection()->getPdo();
        $config = config('database.connections.' . config('database.default'));
        $output = [];

        $output[] = "-- FileTrack Database Backup";
        $output[] = "-- Generated: " . now()->toDateTimeString();
        $output[] = "-- Database: " . ($config['database'] ?? 'unknown');
        $output[] = "SET FOREIGN_KEY_CHECKS=0;";
        $output[] = "";

        // Get all tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Drop + Create table
            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $output[] = "\n-- Table: {$table}";
            $output[] = "DROP TABLE IF EXISTS `{$table}`;";
            $output[] = array_values($create)[1] . ";";
            $output[] = "";

            // Dump rows
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $columns = '`' . implode('`, `', array_keys($rows[0])) . '`';
                foreach ($rows as $row) {
                    $values = array_map(fn($v) =>
                        $v === null ? 'NULL' : $pdo->quote($v), $row);
                    $output[] = "INSERT INTO `{$table}` ({$columns}) VALUES (" . implode(', ', $values) . ");";
                }
            }
            $output[] = "";
        }

        $output[] = "SET FOREIGN_KEY_CHECKS=1;";

        return implode("\n", $output);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
