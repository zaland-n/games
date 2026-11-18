@extends('admin.layout')

@section('title', 'Koppeling bewerken')

@section('admin-content')
    <form action="{{ route('admin.role-permissions.update', $assignment->role_id.'-'.$assignment->permission_id) }}" method="post">
        @csrf
        @method('PUT')
        @include('admin.role-permissions.fields')
        <button class="btn btn-success">Opslaan</button>
        <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
