@extends('admin.layout')

@section('title', 'Permissies beheren')

@section('admin-content')
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-success mb-3">Permissie toevoegen</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Guard</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission)
                <tr>
                    <td>{{ $permission->id }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->guard_name }}</td>
                    <td>
                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary btn-sm">Bewerken</a>
                        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Permissie verwijderen?')">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Nog geen permissies.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
