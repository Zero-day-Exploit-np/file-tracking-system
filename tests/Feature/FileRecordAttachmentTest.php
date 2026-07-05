<?php

use App\Models\Department;
use App\Models\FileRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * File creation is restricted to role=user with can_create_file=true.
 * Admins cannot create files (policy enforced).
 */
it('stores a file record with an attachment', function () {
    Storage::fake('private');

    $department = Department::factory()->create();

    // Must be role:user with can_create_file flag — admins cannot create files
    $user = User::factory()->create([
        'role'            => 'user',
        'department_id'   => $department->id,
        'can_create_file' => true,
    ]);

    $response = $this->actingAs($user)->post(route('files.store'), [
        'file_name'  => 'Contract Document',
        'remarks'    => 'Initial upload',
        'attachment' => UploadedFile::fake()->create(
            'contract.docx', 100,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ),
    ]);

    $response->assertRedirect(route('files.index'));

    $this->assertDatabaseHas('file_records', [
        'file_name'   => 'Contract Document',
        'remarks'     => 'Initial upload',
        'department_id' => $department->id,
    ]);

    $file = FileRecord::where('file_name', 'Contract Document')->first();
    expect($file)->not->toBeNull();
    Storage::disk('private')->assertExists($file->attachment_path);
});

it('downloads a file record attachment', function () {
    Storage::fake('private');

    $department = Department::factory()->create();

    $user = User::factory()->create([
        'role'            => 'user',
        'department_id'   => $department->id,
        'can_create_file' => true,
    ]);

    $attachment = UploadedFile::fake()->create('report.pdf', 120, 'application/pdf');

    // Create the file as a regular user (the only role allowed to create)
    $this->actingAs($user)->post(route('files.store'), [
        'file_name'  => 'Report',
        'remarks'    => 'Upload for download',
        'attachment' => $attachment,
    ]);

    $file = FileRecord::where('file_name', 'Report')->first();
    expect($file)->not->toBeNull();

    // The creator/holder can download
    $download = $this->actingAs($user)->get(route('files.download', $file->uuid));
    $download->assertStatus(200);
    $download->assertHeader('content-disposition');
});
