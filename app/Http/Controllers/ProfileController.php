<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the full profile page.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * Update basic profile info: name, email, phone, contact_number.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email:rfc|max:255|unique:users,email,' . $user->id,
            'phone'          => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $changed = [];

        if ($user->name !== $request->name)                 $changed[] = 'name';
        if ($user->email !== $request->email)               $changed[] = 'email';
        if ($user->phone !== $request->phone)               $changed[] = 'phone';
        if ($user->contact_number !== $request->contact_number) $changed[] = 'contact_number';

        $user->fill([
            'name'           => $request->string('name')->trim()->value(),
            'email'          => $request->string('email')->trim()->lower()->value(),
            'phone'          => $request->string('phone')->trim()->value() ?: null,
            'contact_number' => $request->string('contact_number')->trim()->value() ?: null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if (!empty($changed)) {
            $this->recordAudit('profile_updated', $user, [
                'changed_fields' => $changed,
                'ip'             => $request->ip(),
            ], 'Profile updated by ' . $user->name);
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Upload or replace the profile photo.
     */
    public function uploadPhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|file|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $ext      = strtolower($request->file('photo')->getClientOriginalExtension());
        $filename = 'avatars/' . Str::uuid() . '.' . $ext;
        $request->file('photo')->storeAs('avatars', basename($filename), 'public');

        $user->update(['photo' => $filename]);

        $this->recordAudit('photo_updated', $user, ['ip' => $request->ip()], 'Profile photo updated');

        return back()->with('status', 'photo-updated');
    }

    /**
     * Delete the profile photo.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->update(['photo' => null]);

        $this->recordAudit('photo_deleted', $user, ['ip' => $request->ip()], 'Profile photo removed');

        return back()->with('status', 'photo-deleted');
    }

    /**
     * Change password (requires current password verification).
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validateWithBag('updatePassword', [
            'current_password'      => 'required|current_password',
            'password'              => 'required|min:8|confirmed|different:current_password',
            'password_confirmation' => 'required',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        $this->recordAudit('password_changed', $user, ['ip' => $request->ip()], 'Password changed');

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete account (requires password).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
