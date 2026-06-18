@extends('layouts.app')

@section('content')

<h2>📁 File Timeline</h2>

<h3>
    {{ $file->file_number }} - {{ $file->file_name }}
</h3>

<hr>

<div style="border-left:3px solid #333; padding-left:20px;">

    @foreach($timeline as $log)

    <div style="margin-bottom:20px;">

        <div>
            <b>Action:</b> {{ strtoupper($log->action) }}
        </div>

        <div>
            <b>From:</b>
            {{ $log->fromUser->name ?? 'System' }}
            ({{ $log->fromDept->name ?? '-' }})
        </div>

        <div>
            <b>To:</b>
            {{ $log->toUser->name ?? '-' }}
            ({{ $log->toDept->name ?? '-' }})
        </div>

        <div>
            <small>
                📅 {{ $log->created_at }}
            </small>
        </div>

        <hr>

    </div>

    @endforeach

</div>

@endsection