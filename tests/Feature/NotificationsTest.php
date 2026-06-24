<?php

use App\Notifications\FileTransferredNotification;
use App\Models\Department;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

beforeEach(function () {
    Storage::fake('public');
});

it('shows the notifications page for an authenticated user', function () {
    /** @var \Tests\TestCase $this */
    $department = Department::create([
        'name' => 'Operations',
        'code' => 'OPS',
        'is_active' => true,
    ]);

    /** @var \App\Models\User $sender */
    $sender = User::factory()->create([
        'role' => 'admin',
        'department_id' => $department->id,
    ]);

    /** @var \App\Models\User $recipient */
    $recipient = User::factory()->create([
        'role' => 'user',
        'department_id' => $department->id,
    ]);

    $file = FileRecord::create([
        'department_id' => $department->id,
        'file_name' => 'Policy Document',
        'file_number' => 'DOC-001',
        'status' => 'active',
    ]);

    $transfer = FileTransfer::create([
        'file_id' => $file->id,
        'sender_id' => $sender->id,
        'receiver_id' => $recipient->id,
        'from_department_id' => $department->id,
        'to_department_id' => $department->id,
        'remarks' => 'Unit handoff',
    ]);

    $recipient->notify(new FileTransferredNotification($transfer));

    $response = $this->actingAs($recipient)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertSee('A file has been transferred');
});

it('marks all notifications as read when the action is posted', function () {
    /** @var \Tests\TestCase $this */
    $department = Department::create([
        'name' => 'Operations',
        'code' => 'OPS',
        'is_active' => true,
    ]);

    /** @var \App\Models\User $sender */
    $sender = User::factory()->create([
        'role' => 'admin',
        'department_id' => $department->id,
    ]);

    /** @var \App\Models\User $recipient */
    $recipient = User::factory()->create([
        'role' => 'user',
        'department_id' => $department->id,
    ]);

    $file = FileRecord::create([
        'department_id' => $department->id,
        'file_name' => 'Policy Document',
        'file_number' => 'DOC-002',
        'status' => 'active',
    ]);

    $transfer = FileTransfer::create([
        'file_id' => $file->id,
        'sender_id' => $sender->id,
        'receiver_id' => $recipient->id,
        'from_department_id' => $department->id,
        'to_department_id' => $department->id,
        'remarks' => 'Unit handoff',
    ]);

    $recipient->notify(new FileTransferredNotification($transfer));

    expect($recipient->unreadNotifications)->toHaveCount(1);

    $response = $this->actingAs($recipient)->post(route('notifications.readAll'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertSame(0, $recipient->fresh()->unreadNotifications->count());
});
