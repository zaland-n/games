@extends('admin.layout')

@section('title', 'Koppeling bewerken')

@section('admin-content')
    <form action="{{ route('admin.user-roles.update', $assignment->user_id.'-'.$assignment->role_id) }}" method="post">
        @csrf
        @method('PUT')
        @include('admin.user-roles.fields')
        <button class="btn btn-success">Opslaan</button>
        <a href="{{ route('admin.user-roles.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
