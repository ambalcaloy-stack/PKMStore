@extends('layouts.app')

@section('content')
<div class="login-content row g-0">
    <div class="login-visual col-md-7 d-flex align-items-end p-4 p-lg-5 text-white">
        <div class="mb-2 mb-lg-4">
            <p class="text-uppercase small fw-bold mb-2">PKM Research System</p>
            <h1 class="display-5 fw-bold mb-2">Your campus essentials, in one place.</h1>
            <p class="lead mb-0">Sign in to browse products, manage your cart, and track your school supply orders.</p>
        </div>
    </div>
    <div class="login-panel col-md-5 d-flex align-items-center">
        <div class="w-100 p-4 p-lg-5">
            <p class="text-success text-uppercase small fw-bold mb-2">Welcome back</p>
            <h2 class="fw-bold mb-2">Sign in to continue</h2>
            <p class="text-muted mb-4">Use your PKM account to access the store.</p>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required autofocus>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input id="password" type="password" name="password" class="form-control form-control-lg" required>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Sign in</button>
            </form>

            <p class="text-muted text-center mt-4 mb-0">New to PKM? <a class="text-success fw-semibold" href="{{ route('register') }}">Create an account</a></p>
        </div>
    </div>
</div>
@endsection
