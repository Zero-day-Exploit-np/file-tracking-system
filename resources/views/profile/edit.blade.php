@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.update-password-form')
    @include('profile.partials.delete-user-form')

</div>
@endsection