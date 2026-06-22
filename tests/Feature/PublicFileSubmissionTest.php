<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores a public file submission successfully', function () {
    Storage::fake('public');

    $response = $this->post(route('public-files.store'), [
        'applicant_name' => 'Aarav Sharma',
        'email' => 'aarav@example.com',
        'contact_number' => '9876543210',
        'subject' => 'Request for document review',
        'remarks' => 'Please review the attached file.',
        'attachment' => UploadedFile::fake()->create('sample.pdf', 120, 'application/pdf'),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('public_files', [
        'email' => 'aarav@example.com',
        'subject' => 'Request for document review',
    ]);
});
