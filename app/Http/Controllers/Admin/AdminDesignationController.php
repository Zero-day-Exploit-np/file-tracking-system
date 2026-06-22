<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Department;
use Illuminate\Http\Request;

class AdminDesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::with('department')
            ->where('department_id', auth()->user()->department_id)
            ->latest()
            ->paginate(15);

        return view('admin.designations.index', compact('designations'));
    }

    public function create()
    {
        return view('admin.designations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        Designation::create([
            'department_id' => auth()->user()->department_id,
            'name'          => $request->name,
            'is_active'     => $request->status,
        ]);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation created successfully.');
    }

    public function edit($id)
    {
        $designation = Designation::where('department_id', auth()->user()->department_id)
            ->findOrFail($id);

        return view('admin.designations.edit', compact('designation'));
    }

    public function update(Request $request, $id)
    {
        $designation = Designation::where('department_id', auth()->user()->department_id)
            ->findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $designation->update([
            'name'      => $request->name,
            'is_active' => $request->status,
        ]);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function show($id)
    {
        return redirect()->route('admin.designations.index');
    }

    public function destroy($id)
    {
        $designation = Designation::where('department_id', auth()->user()->department_id)
            ->findOrFail($id);

        $designation->delete();

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation deleted.');
    }
}
