@extends('firebase.app')

@section('content')

    <!-- Link to hero.css -->
    <link rel="stylesheet" href="{{ asset('css/hero.css') }}">

    <div class="hero">
        <h2>Welcome to {{ config('app.name') }}</h2>
        <p>Your health is our priority.</p>
    </div>

@endsection

