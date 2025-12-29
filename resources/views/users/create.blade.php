@extends('layouts.app')
@section('title','Crear Usuario')
@section('content')
<h2>Crear Usuario</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('users.store') }}" method="POST">
    @include('users._form')
</form>
@endsection
