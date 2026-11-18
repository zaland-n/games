@extends('admin.layout')

@section('title', 'Rollen aan gebruikers koppelen')

@section('admin-content')
    <a href="{{ route('admin.user-roles.create') }}" class="btn btn-success mb-3">Koppeling toevoegen</a>

    <table class="table">
        <thead>
            <tr>
                <th>Gebruiker-ID</th>
                <th>Gebruiker</th>
                <th>E-mailadres</th>
                <th>Rol-ID</th>
                <th>Rol</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->user_id }}</td>
                    <td>{{ $assignment->user_name }}</td>
                    <td>{{ $assignment->email }}</td>
                    <td>{{ $assignment->role_id }}</td>
                    <td>{{ $assignment->role_name }}</td>
                    <td>
                        <a href="{{ route('admin.user-roles.edit', $assignment->user_id.'-'.$assignment->role_id) }}" class="btn btn-primary btn-sm">Bewerken</a>
                        <form action="{{ route('admin.user-roles.destroy', $assignment->user_id.'-'.$assignment->role_id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Koppeling verwijderen?')">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Nog geen rollen aan gebruikers gekoppeld.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
