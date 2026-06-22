<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('department_id', auth()->user()->department_id)
            ->where('role', 'user')
            ->with('designation')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $designations = Designation::where('department_id', auth()->user()->department_id)->get();
        return view('admin.users.create', compact('designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email:rfc|max:255|unique:users,email',
            'password'       => 'required|min:8',
            'designation_id' => 'required|exists:designations,id',
            'contact_number' => 'nullable|string|max:20',
            // Secure upload: MIME type + extension + 2 MB max
            'photo'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'can_create_file'=> 'nullable|boolean',
        ]);

        $data = [
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'designation_id'  => $request->designation_id,
            'department_id'   => auth()->user()->department_id,
            'role'            => 'user',
            'contact_number'  => $request->contact_number,
            'can_create_file' => $request->boolean('can_create_file'),
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storePhoto($request);
        }

        $user = User::create($data);

        $this->recordAudit('user_created', $user, [
            'name'  => $user->name,
            'email' => $user->email,
            'ip'    => $request->ip(),
        ], 'User created by admin: ' . auth()->user()->name);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = User::where('department_id', auth()->user()->department_id)
            ->with('designation')
            ->findOrFail($id);

        return view('admin.users.index', compact('user'));
    }

    public function edit($id)
    {
        $user         = User::where('department_id', auth()->user()->department_id)->findOrFail($id);
        $designations = Designation::where('department_id', auth()->user()->department_id)->get();

        return view('admin.users.edit', compact('user', 'designations'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('department_id', auth()->user()->department_id)->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email:rfc|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'           => $request->name,
            'email'          => $request->email,
            'designation_id' => $request->designation_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::where('department_id', auth()->user()->department_id)->findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->recordAudit('user_deleted', $user, [
            'name'  => $user->name,
            'email' => $user->email,
            'ip'    => request()->ip(),
        ], 'User deleted by admin: ' . auth()->user()->name);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Store profile photo securely with a random UUID filename.
     */
    private function storePhoto(Request $request): string
    {
        $file      = $request->file('photo');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename  = Str::uuid() . '.' . $extension;

        return $file->storeAs('uploads/users', $filename, 'public');
    }
}
