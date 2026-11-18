@extends('admin.layout')

@section('title', 'Permissie bewerken')

@section('admin-content')
    <form action="{{ route('admin.permissions.update', $permission) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Naam</label>
            <input id="name" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required maxlength="255">
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-success">Opslaan</button>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
