@extends('layout')

@section('title', 'About')

@section('content')
<h1>Wally's Widget Company Pack Calcualator</h1>
<p>This simple website calculates the best pack combinations to use for a given widget total. </p>
<br>
<p>Specifically, it will provide the best pack combinations based on three rules:</p>
<ul>
    <li>It will only give a result in whole packs; packs are not broken open.</li>
    <li>It will send out no more widgets than necessary.</li>
    <li>It will send out as few packs as possible.</li>
</ul>
<br>
<p>To use the website, there must be at least two size packs created which cannot have a capacity lower than two widgets and higher than 10000000 widgets. Once you create at least two packs, you can then enter a value in for the number of widgets ordered. Here you must enter more than one widget and no more than 1000000. This is due to calculation time where attempting to calculate more than 50,000 will result in a timeout on the server. Regardless, you may notice this happen even with the total widget constraints i.e, packs capacity lower than 10 with 1000000 widgets ordered.</p>
@endsection
