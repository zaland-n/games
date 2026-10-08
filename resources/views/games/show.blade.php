@extends('base')

@section('title', '🎯 Game Details')

@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">{{ $game->game_name }}</h3>
            <p><strong>Platform:</strong> {{ $game->platform }}</p>
            <p><strong>Genre:</strong> {{ $game->genre }}</p>
            <p><strong>Rating:</strong> {{ $game->rating }}/10</p>
            <a href="/games" class="btn btn-secondary">Back</a>
            <a href="/games/edit/{{ $game->id }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
@endsection
