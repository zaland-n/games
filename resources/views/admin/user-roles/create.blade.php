@extends('admin.layout')

@section('title', 'Rol aan gebruiker koppelen')

@section('admin-content')
    <form action="{{ route('admin.user-roles.store') }}" method="post">
        @csrf
        @include('admin.user-roles.fields')
        <button class="btn btn-success">Koppelen</button>
        <a href="{{ route('admin.user-roles.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
