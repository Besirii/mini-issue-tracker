@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Create an account</h1>

                    @include('partials.validation-errors')

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}"
                                   class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}"
                                   class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">Register</button>
                    </form>

                    <p class="text-muted small mt-3 mb-0">
                        Already registered? <a href="{{ route('login') }}">Log in</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
