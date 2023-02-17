@extends('layout')

@section('title', $packSize->packSize)

@section('content')

<div class="post-item">
    <div class="post-content">
        <h1>{{ $packSize->packSize }}</h1>

        <a href="{{ route('packs.edit', [$packSize]) }}">Edit Pack Size</a>

        <form method="POST" action="{{ route('packs.destroy', [$packSize]) }}">
            @csrf
            @method('DELETE')
            <button class="delete" type="submit">Delete Pack Size</button>
        </form>
    </div>
</div>

@endsection