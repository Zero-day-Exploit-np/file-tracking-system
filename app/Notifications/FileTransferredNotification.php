<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\FileTransfer;

class FileTransferredNotification extends Notification
{
    use Queueable;

    public $transfer;

    public function __construct(FileTransfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function via($notifiable)
    {
        return ['database']; // store in DB
    }

    public function toDatabase($notifiable)
    {
        return [
            'file_id'    => $this->transfer->file_id,
            'file_title' => $this->transfer->file->file_name ?? 'Unknown File',
            'sender'     => $this->transfer->sender->name ?? 'System',
            'receiver'   => $this->transfer->receiver->name ?? 'N/A',
            'remarks'    => $this->transfer->remarks,
            'message'    => 'A file has been transferred to you',
        ];
    }
}
