<h2>Transfer Requests</h2>

<table border="1">

    <tr>
        <th>File</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    @foreach($requests as $request)

    <tr>

        <td>
            {{ $request->file_id }}
        </td>

        <td>
            {{ $request->status }}
        </td>

        <td>

            <form action="{{ route('admin.transfer.approve',$request->id) }}"
                method="POST">

                @csrf

                <button>
                    Approve
                </button>

            </form>

            <form action="{{ route('admin.transfer.reject',$request->id) }}"
                method="POST">

                @csrf

                <button>
                    Reject
                </button>

            </form>

        </td>

    </tr>

    @endforeach

</table>