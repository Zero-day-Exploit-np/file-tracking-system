<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Files</h2>

        <a href="{{ route('files.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded">
            + Create File
        </a>

        <table class="w-full mt-4 border">
            <thead>
                <tr>
                    <th class="border p-2">File No</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Department</th>
                    <th class="border p-2">Creator</th>
                </tr>
            </thead>

            <tbody>
                @foreach($files as $file)
                <tr>
                    <td class="border p-2">{{ $file->file_number }}</td>
                    <td class="border p-2">{{ $file->file_name }}</td>
                    <td class="border p-2">{{ $file->department->name }}</td>
                    <td class="border p-2">{{ $file->creator->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>