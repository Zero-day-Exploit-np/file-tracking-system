<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->value();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $departments = $query->latest()->paginate(15)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:departments,name',
            'code'      => 'required|string|max:50|unique:departments,code|alpha_num',
            'is_active' => 'required|boolean',
        ]);

        Department::create([
            'name'      => $request->string('name')->trim()->value(),
            'code'      => strtoupper($request->string('code')->trim()->value()),
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code'      => 'required|string|max:50|unique:departments,code,' . $department->id . '|alpha_num',
            'is_active' => 'required|boolean',
        ]);

        $department->update([
            'name'      => $request->string('name')->trim()->value(),
            'code'      => strtoupper($request->string('code')->trim()->value()),
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        // Prevent deleting if users are assigned
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a department that has users assigned to it.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
