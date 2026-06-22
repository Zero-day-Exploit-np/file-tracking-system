<?php

namespace App\Http\Controllers;

use App\Models\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicFileController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'attachment' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')
                ->store('uploads', 'public');
        }

        PublicFile::create($validated);

        return back()->with('success', 'Your file submission has been received successfully.');
    }
}
