@extends('admin.layout')

@section('title', 'Rollen beheren')

@section('admin-content')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-success mb-3">Rol toevoegen</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Guard</th>
                <th>Permissies</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->guard_name }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">Bewerken</a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Rol verwijderen?')">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Nog geen rollen.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
