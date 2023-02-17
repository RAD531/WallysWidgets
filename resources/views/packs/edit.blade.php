@extends('layout')

@section('title', 'Update Pack Size '. $packSize->packSize)

@section('content')

<h1>Update Pack Size {{ $packSize->packSize }}</h1>

<form method="POST" action="{{ route('packs.update', [$packSize]) }}">
    @csrf
    @method('PUT')

    <label>Pack Size</label>
    <input class="@error('packSize') error-border @enderror" type="number" name="packSize" value="{{ old('packSize', $packSize->packSize) }} " min="1" max="10000000">
    @error('title')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

    <button type="submit">Update</button>
</form>

@endsection