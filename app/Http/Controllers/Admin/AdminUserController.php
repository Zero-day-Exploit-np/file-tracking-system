<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'email'          => 'required|email|unique:users',
            'password'       => 'required|min:6',
            'designation_id' => 'required|exists:designations,id',
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
            $filename = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->move(public_path('uploads/users'), $filename);
            $data['photo'] = $filename;
        }

        User::create($data);

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
            'email' => 'required|email|unique:users,email,' . $user->id,
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

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
