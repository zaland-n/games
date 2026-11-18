@extends('admin.layout')

@section('title', 'Permissie toevoegen')

@section('admin-content')
    <form action="{{ route('admin.permissions.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Naam</label>
            <input id="name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-success">Opslaan</button>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
