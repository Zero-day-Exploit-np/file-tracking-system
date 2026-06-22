<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class AdminDesignationController extends Controller
{
    /** Resolve designation by UUID, scoped to admin's department */
    private function resolveDesignation(string $uuid): Designation
    {
        return Designation::where('uuid', $uuid)
            ->where('department_id', auth()->user()->department_id)
            ->firstOrFail();
    }

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
            'name'          => $request->string('name')->trim()->value(),
            'is_active'     => (bool) $request->status,
        ]);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation created successfully.');
    }

    public function show(string $designation)
    {
        return redirect()->route('admin.designations.index');
    }

    public function edit(string $designation)
    {
        $model = $this->resolveDesignation($designation);
        return view('admin.designations.edit', ['designation' => $model]);
    }

    public function update(Request $request, string $designation)
    {
        $model = $this->resolveDesignation($designation);

        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $model->update([
            'name'      => $request->string('name')->trim()->value(),
            'is_active' => (bool) $request->status,
        ]);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(string $designation)
    {
        $model = $this->resolveDesignation($designation);
        $model->delete();

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation deleted.');
    }
}
