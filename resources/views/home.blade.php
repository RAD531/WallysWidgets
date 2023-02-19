@extends('layout')

@section('title', 'Home')

@section('content')

<div class="row">
    <div class="column">
        <div class="post-item">
            <div class="post-content">
                <form method="GET" action="{{ route('calculatePacks') }}">
                    @csrf

                    <label>Widgets</label>
                    <input class="@error('widgets') error-border @enderror" type="number" name="widgets" value="{{ old('widgets', session('widgets')) }}" min="2" max="1000000">
                    @error('title')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                    <button type="submit">Get Best Pack Sizes</button>
                </form>
            </div>
        </div>

        @if (session('globalPackSizes'))
            <div class="post-item">
                <div class="post-content">
                    @foreach (Session::get('globalPackSizes') as $key => $value)
                        <p>{{$key}} Pack: {{ $value }}</p>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <div class="column">
        @forelse ($packSizes as $packSize)
        <div class="post-item">
            <div class="post-content">

                <form method="POST" action="{{ route('packs.update', $packSize->id) }}">
                @csrf
                @method('PUT')

                <label>Pack Size</label>
                <input class="@error('packSize') error-border @enderror" type="number" name="packSize" value="{{ old('packSize', $packSize->packSize) }}" min="1" max="10000000">
                @error('title')
                    <div class="error">
                        {{ $message }}
                    </div>
                @enderror

                <button type="submit">Update</button>
                </form>

                <form method="POST" action="{{ route('packs.destroy', [$packSize->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="delete" type="submit">Delete Pack Size</button>
                </form>

            </div>
        </div>

        @empty
        <h2>There are no Pack Sizes yet.</h2>
        @endforelse
    </div>
</div>

@endsection
