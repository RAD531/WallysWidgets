@extends('layout')

@section('title', 'Create a new pack size')

@section('content')

<h1>Create a New Pack Size</h1>

<form method="POST" action="{{ route('packs.store') }}">
    @csrf

    <label>Pack Size</label>
    <input class="@error('packSize') error-border @enderror" type="number" name="packSize" value="{{ old('packSize') }}" min="2" max="10000000">
    @error('title')
        <div class="error">
            {{ $message }}
        </div>
    @enderror

    <button type="submit">Submit</button>
</form>

@endsection