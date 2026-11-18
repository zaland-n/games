@extends('admin.layout')

@section('title', 'Permissie aan rol koppelen')

@section('admin-content')
    <form action="{{ route('admin.role-permissions.store') }}" method="post">
        @csrf
        @include('admin.role-permissions.fields')
        <button class="btn btn-success">Koppelen</button>
        <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
