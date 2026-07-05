<?php

namespace App\Notifications;

use App\Models\TransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to the requester when their transfer request is approved or rejected.
 */
class TransferStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly TransferRequest $transferReq,
        public readonly string $status,        // 'approved' | 'rejected'
        public readonly string $adminName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isApproved = $this->status === 'approved';

        return [
            'type'        => 'transfer_' . $this->status,
            'title'       => $isApproved ? 'Transfer Approved' : 'Transfer Rejected',
            'message'     => $isApproved
                ? 'Your transfer request was approved by ' . $this->adminName
                : 'Your transfer request was rejected by ' . $this->adminName,
            'icon'        => $isApproved ? 'circle-check' : 'circle-xmark',
            'color'       => $isApproved ? 'green' : 'red',
            'url'         => route('files.show', $this->transferReq->file->uuid, false),
            'file_title'  => $this->transferReq->file->file_name ?? 'Unknown File',
            'file_number' => $this->transferReq->file->file_number ?? '',
            'file_uuid'   => $this->transferReq->file->uuid ?? null,
            'from_dept'   => $this->transferReq->fromDept->name ?? '',
            'to_dept'     => $this->transferReq->toDept->name ?? '',
            'target_user' => $this->transferReq->receiver->name ?? '',
            'status'      => $this->status,
        ];
    }
}
