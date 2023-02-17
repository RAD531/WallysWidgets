<!DOCTYPE html>
<html>
<head>
    <title>
        @yield('title', 'Blog')
    </title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body>

<ul class="nav">
    <li><a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
    <li><a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>

    <li><a class="{{ request()->routeIs('packs.create') ? 'active' : '' }}" href="{{ route('packs.create') }}">Create Pack Size</a></li>
    <li class="title">Wally's Widget Company</li>
</ul>


@includeWhen($errors->any(), '_errors')


@if (session('success'))
    <div class="flash-success">
        {{ session('success') }}
    </div>
@endif

<div class="main">
    @yield('content')
</div>

</body>
</html>
