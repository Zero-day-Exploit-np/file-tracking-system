<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">
            File History: {{ $file->file_number }}
        </h2>

        <p><b>Name:</b> {{ $file->file_name }}</p>

        <h3 class="mt-4 font-bold">Transfer History</h3>

        <table class="w-full border mt-2">
            <tr>
                <th class="border p-2">From</th>
                <th class="border p-2">To</th>
                <th class="border p-2">Remarks</th>
                <th class="border p-2">Date</th>
            </tr>

            @foreach($file->transfers as $t)
            <tr>
                <td class="border p-2">{{ $t->fromUser->name }}</td>
                <td class="border p-2">{{ $t->toUser->name }}</td>
                <td class="border p-2">{{ $t->remarks }}</td>
                <td class="border p-2">{{ $t->created_at }}</td>
            </tr>
            @endforeach
        </table>

    </div>
</x-app-layout>