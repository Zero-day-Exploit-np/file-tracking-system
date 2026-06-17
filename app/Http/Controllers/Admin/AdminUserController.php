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
            ->latest()
            ->paginate(10);

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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'designation_id' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'designation_id' => $request->designation_id,
            'department_id' => auth()->user()->department_id,
            'role' => 'user'
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $designations = Designation::where('department_id', auth()->user()->department_id)->get();

        return view('admin.users.edit', compact('user', 'designations'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'designation_id' => $request->designation_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
