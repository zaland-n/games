@extends('admin.layout')

@section('title', 'Permissies aan rollen koppelen')

@section('admin-content')
    <a href="{{ route('admin.role-permissions.create') }}" class="btn btn-success mb-3">Koppeling toevoegen</a>

    <table class="table">
        <thead>
            <tr>
                <th>Rol-ID</th>
                <th>Rol</th>
                <th>Permissie-ID</th>
                <th>Permissie</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->role_id }}</td>
                    <td>{{ $assignment->role_name }}</td>
                    <td>{{ $assignment->permission_id }}</td>
                    <td>{{ $assignment->permission_name }}</td>
                    <td>
                        <a href="{{ route('admin.role-permissions.edit', $assignment->role_id.'-'.$assignment->permission_id) }}" class="btn btn-primary btn-sm">Bewerken</a>
                        <form action="{{ route('admin.role-permissions.destroy', $assignment->role_id.'-'.$assignment->permission_id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Koppeling verwijderen?')">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Nog geen permissies aan rollen gekoppeld.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
