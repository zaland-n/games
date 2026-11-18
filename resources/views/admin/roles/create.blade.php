@extends('admin.layout')

@section('title', 'Rol toevoegen')

@section('admin-content')
    <form action="{{ route('admin.roles.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Naam</label>
            <input id="name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-success">Opslaan</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
