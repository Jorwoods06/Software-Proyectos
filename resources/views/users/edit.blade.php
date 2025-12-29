@extends('layouts.app')
@section('title','Editar Usuario')
@section('content')
<h2>Editar Usuario</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @method('PUT')
    @include('users._form')
</form>
@endsection
