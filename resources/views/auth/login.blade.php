@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Log in</h1>

                    @include('partials.validation-errors')

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}"
                                   class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button class="btn btn-primary w-100">Log in</button>
                    </form>

                    <p class="text-muted small mt-3 mb-0">
                        No account? <a href="{{ route('register') }}">Register</a>.
                    </p>
                    <p class="text-muted small mb-0">Demo: <code>alice@example.com</code> / <code>password</code></p>
                </div>
            </div>
        </div>
    </div>
@endsection
