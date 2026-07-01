<?php

/**
 * Public File Upload has been removed from this system.
 * Public users can now search files using the Public File Search feature.
 *
 * @see PublicFileSearchController
 * @see /public/file-search
 */

it('public file search page loads successfully', function () {
    $response = $this->get(route('public.file.search'));
    $response->assertStatus(200);
});

it('returns no result for nonexistent file number', function () {
    $response = $this->get(route('public.file.search.result', ['file_number' => 'INVALID-00000']));
    $response->assertSessionHas('search_error');
});
