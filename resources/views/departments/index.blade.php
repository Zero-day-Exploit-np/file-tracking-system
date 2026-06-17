<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                Departments
            </h1>

            <a href="{{ route('departments.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded">
                + Add Department
            </a>
        </div>

        <!-- Search Box -->
        <form method="GET" action="{{ route('departments.index') }}" class="mb-4">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search department..."
                class="border rounded px-3 py-2 w-64">

            <button
                type="submit"
                class="bg-gray-700 text-white px-4 py-2 rounded">
                Search
            </button>
        </form>

        <!-- Table -->
        <div class="bg-white shadow rounded">
            <table class="table-auto w-full border">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Code</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($departments as $department)
                    <tr>
                        <td class="px-4 py-2 border">{{ $department->id }}</td>
                        <td class="px-4 py-2 border">{{ $department->name }}</td>
                        <td class="px-4 py-2 border">{{ $department->code }}</td>

                        <!-- Status Badge -->
                        <td class="px-4 py-2 border">
                            @if($department->is_active)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                Active
                            </span>
                            @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">
                                Inactive
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-2 border">
                            <a href="{{ route('departments.edit', $department) }}"
                                class="text-blue-600 mr-2">
                                Edit
                            </a>

                            <form action="{{ route('departments.destroy', $department) }}"
                                method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Delete this department?')"
                                    class="text-red-600">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No departments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $departments->links() }}
        </div>

    </div>
</x-app-layout>