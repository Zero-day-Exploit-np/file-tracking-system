<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Create File</h2>

        <form method="POST" action="{{ route('files.store') }}">
            @csrf

            <input type="text" name="file_name" placeholder="File Name"
                class="border p-2 w-full mb-2">

            <select name="department_id" class="border p-2 w-full mb-2">
                <option value="">Select Department</option>
                @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>

            <textarea name="remarks" placeholder="Remarks"
                class="border p-2 w-full mb-2"></textarea>

            <button class="bg-blue-600 text-white px-4 py-2">
                Create File
            </button>

        </form>

    </div>
</x-app-layout>