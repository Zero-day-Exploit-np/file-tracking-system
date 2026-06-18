<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with(['department', 'designation'])->latest()->get();
        return view('users.index', compact('users'));
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
    public function create()
    {
        $departments = Department::all();
        $designations = Designation::all();

        return view(
            'users.create',
            compact('departments', 'designations')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'role' => 'required',

            // extra fields
            'contact_number' => 'nullable',
            'photo' => 'nullable|image|max:2048',
            'can_create_file' => 'nullable|boolean',
        ]);

        // prepare data
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,

            // new fields
            'contact_number' => $request->contact_number,
            'can_create_file' => $request->has('can_create_file'),
        ];

        // photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/users'), $filename);

            $data['photo'] = $filename;
        }

        // create user
        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }
}
