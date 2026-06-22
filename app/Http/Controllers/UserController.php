<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['department', 'designation'])->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments  = Department::orderBy('name')->get();
        $designations = Designation::with('department')->orderBy('name')->get();
        return view('users.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email:rfc,dns|max:255|unique:users,email',
            'password'       => 'required|min:8|confirmed',
            'role'           => 'required|in:super_admin,admin,user',
            'department_id'  => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'contact_number' => 'nullable|string|max:20',
            // Strict: MIME type + extension + size limit (2 MB)
            'photo'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'can_create_file'=> 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'email', 'role', 'department_id', 'designation_id', 'contact_number']);
        $data['password']        = Hash::make($request->password);
        $data['can_create_file'] = $request->boolean('can_create_file');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storePhoto($request);
        }

        $user = User::create($data);

        $this->recordAudit('user_created', $user, [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
            'ip'    => $request->ip(),
        ], 'Admin user created by ' . auth()->user()->name);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['department', 'designation']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $departments  = Department::orderBy('name')->get();
        $designations = Designation::with('department')->orderBy('name')->get();
        return view('users.edit', compact('user', 'departments', 'designations'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email:rfc,dns|max:255|unique:users,email,' . $user->id,
            'role'           => 'required|in:super_admin,admin,user',
            'department_id'  => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'contact_number' => 'nullable|string|max:20',
            'password'       => 'nullable|min:8|confirmed',
        ]);

        // Only allow updating safe fields (not id, remember_token, etc.)
        $data = $request->only(['name', 'email', 'role', 'department_id', 'designation_id', 'contact_number']);
        $data['can_create_file'] = $request->boolean('can_create_file');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->recordAudit('user_deleted', $user, [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
            'ip'    => request()->ip(),
        ], 'User deleted by ' . auth()->user()->name);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Store a profile photo securely.
     * - Uses random filename (never trusts user-provided name)
     * - Stores in storage/app/public/uploads/users (not public_path)
     * - Returns stored path
     */
    private function storePhoto(Request $request): string
    {
        $file      = $request->file('photo');
        $extension = $file->getClientOriginalExtension();
        $filename  = Str::uuid() . '.' . strtolower($extension);

        return $file->storeAs('uploads/users', $filename, 'public');
    }
}
