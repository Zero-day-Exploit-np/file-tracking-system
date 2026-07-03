<?php

namespace App\Notifications;

use App\Models\FileTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to the file receiver upon a direct transfer.
 * No approval notifications — all transfers are immediate.
 */
class FileTransferredNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly FileTransfer $transfer) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $file   = $this->transfer->file;
        $sender = $this->transfer->sender;

        return [
            'type'        => 'file_received',
            'message'     => 'New file received: ' . ($file->file_name ?? 'Unknown File'),
            'file_id'     => $this->transfer->file_id,
            'file_title'  => $file->file_name   ?? 'Unknown File',
            'file_number' => $file->file_number ?? '',
            'sender'      => $sender->name       ?? 'System',
            'remarks'     => $this->transfer->remarks,
        ];
    }
}
