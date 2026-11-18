@extends('base')

@section('content')
    <nav class="mb-4">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary">Permissies beheren</a>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">Rollen beheren</a>
        <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-outline-primary">Permissies aan rollen koppelen</a>
        <a href="{{ route('admin.user-roles.index') }}" class="btn btn-outline-primary">Rollen aan gebruikers koppelen</a>
    </nav>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @yield('admin-content')
@endsection
