<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Create User</h2>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <input type="text" name="name" placeholder="Name" class="border p-2 w-full mb-2">

            <input type="email" name="email" placeholder="Email" class="border p-2 w-full mb-2">

            <input type="password" name="password" placeholder="Password" class="border p-2 w-full mb-2">

            <select name="role" class="border p-2 w-full mb-2">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <select name="department_id" class="border p-2 w-full mb-2">
                <option value="">Select Department</option>
                @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>

            <select name="designation_id" class="border p-2 w-full mb-2">
                <option value="">Select Designation</option>
                @foreach($designations as $des)
                <option value="{{ $des->id }}">{{ $des->name }}</option>
                @endforeach
            </select>

            <button class="bg-blue-600 text-white px-4 py-2">
                Create User
            </button>

        </form>

    </div>
</x-app-layout>