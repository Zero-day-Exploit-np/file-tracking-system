<?php

use App\Models\Department;
use App\Models\FileRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores a file record with an attachment', function () {
    Storage::fake('private');

    $department = Department::factory()->create();
    $admin = User::factory()->create([
        'role' => 'admin',
        'department_id' => $department->id,
    ]);

    $response = $this->actingAs($admin)->post(route('files.store'), [
        'department_id' => $department->id,
        'file_name' => 'Contract Document',
        'remarks' => 'Initial upload',
        'attachment' => UploadedFile::fake()->create('contract.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
    ]);

    $response->assertRedirect(route('files.index'));
    $this->assertDatabaseHas('file_records', [
        'file_name' => 'Contract Document',
        'remarks' => 'Initial upload',
        'department_id' => $department->id,
    ]);

    $file = FileRecord::where('file_name', 'Contract Document')->first();
    expect($file)->not->toBeNull();
    Storage::disk('private')->assertExists($file->attachment_path);
});

it('downloads a file record attachment', function () {
    Storage::fake('private');

    $department = Department::factory()->create();
    $admin = User::factory()->create([
        'role' => 'admin',
        'department_id' => $department->id,
    ]);

    $attachment = UploadedFile::fake()->create('report.pdf', 120, 'application/pdf');

    $this->actingAs($admin)->post(route('files.store'), [
        'department_id' => $department->id,
        'file_name' => 'Report',
        'remarks' => 'Upload for download',
        'attachment' => $attachment,
    ]);

    $file = FileRecord::where('file_name', 'Report')->first();
    expect($file)->not->toBeNull();

    $download = $this->actingAs($admin)->get(route('files.download', $file->uuid));
    $download->assertStatus(200);
    $download->assertHeader('content-disposition');
});
