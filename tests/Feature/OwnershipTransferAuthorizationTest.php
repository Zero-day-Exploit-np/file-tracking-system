<?php

use App\Models\Department;
use App\Models\FileRecord;
use App\Models\User;

function makeTransferUser(Department $department, string $role = 'user'): User
{
    return User::factory()->create([
        'role' => $role,
        'department_id' => $department->id,
        'is_active' => true,
        'can_create_file' => $role === 'user',
    ]);
}

it('allows only the current owner to transfer through direct and department ownership changes', function () {
    /** @var \Tests\TestCase $this */
    $sourceDepartment = Department::factory()->create(['name' => 'Source Department']);
    $financeDepartment = Department::factory()->create(['name' => 'Finance Department']);
    $accountsDepartment = Department::factory()->create(['name' => 'Accounts Department']);

    $userA = makeTransferUser($sourceDepartment);
    $userB = makeTransferUser($sourceDepartment);
    $financeAdmin = makeTransferUser($financeDepartment, 'admin');
    $financeUser = makeTransferUser($financeDepartment);
    $accountsAdmin = makeTransferUser($accountsDepartment, 'admin');

    $file = FileRecord::create([
        'department_id' => $sourceDepartment->id,
        'created_by' => $userA->id,
        'current_user_id' => $userA->id,
        'file_name' => 'Ownership Test',
        'file_number' => 'OWN-001',
        'status' => 'active',
    ]);

    $this->actingAs($userA)->post(route('files.transfer.store'), [
        'file_record_uuid' => $file->uuid,
        'destination_type' => 'same',
        'to_user_id' => $userB->id,
    ])->assertRedirect(route('files.index'));

    expect($file->fresh()->current_user_id)->toBe($userB->id);

    $this->actingAs($userA)
        ->get(route('files.transfer.create', $file->uuid))
        ->assertForbidden();

    $this->actingAs($userB)->post(route('files.transfer.store'), [
        'file_record_uuid' => $file->uuid,
        'destination_type' => 'other',
        'department_id' => $financeDepartment->id,
    ])->assertRedirect(route('files.index'));

    $file->refresh();
    expect($file->current_user_id)->toBe($financeAdmin->id)
        ->and($file->department_id)->toBe($financeDepartment->id);

    $this->actingAs($financeAdmin)->post(route('files.transfer.store'), [
        'file_record_uuid' => $file->uuid,
        'destination_type' => 'same',
        'to_user_id' => $financeUser->id,
    ])->assertRedirect(route('files.index'));

    expect($file->fresh()->current_user_id)->toBe($financeUser->id);

    $this->actingAs($financeAdmin)
        ->get(route('files.transfer.create', $file->uuid))
        ->assertForbidden();

    $this->actingAs($financeUser)->post(route('files.transfer.store'), [
        'file_record_uuid' => $file->uuid,
        'destination_type' => 'other',
        'department_id' => $accountsDepartment->id,
    ])->assertRedirect(route('files.index'));

    $file->refresh();
    expect($file->current_user_id)->toBe($accountsAdmin->id)
        ->and($file->department_id)->toBe($accountsDepartment->id);

    $this->actingAs($accountsAdmin)
        ->get(route('files.transfer.create', $file->uuid))
        ->assertOk();
});

it('does not let a super admin transfer a file they do not own', function () {
    /** @var \Tests\TestCase $this */
    $department = Department::factory()->create();
    $owner = makeTransferUser($department);
    $superAdmin = makeTransferUser($department, 'super_admin');

    $file = FileRecord::create([
        'department_id' => $department->id,
        'created_by' => $owner->id,
        'current_user_id' => $owner->id,
        'file_name' => 'Super Admin Ownership Test',
        'file_number' => 'OWN-002',
        'status' => 'active',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('files.transfer.create', $file->uuid))
        ->assertForbidden();
});
