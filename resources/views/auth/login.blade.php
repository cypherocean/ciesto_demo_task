@extends('auth.layout.app')

@section('title')
Login
@endsection

@section('styles')
@endsection

@section('content')
<div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative" style="background:url({{ asset('assets/images/big/auth-bg.jpg') }}) no-repeat center center;">
    <div class="auth-box row">
        <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url({{ asset('assets/images/big/login-bg.jpg') }});">
        </div>
        <div class="col-lg-5 col-md-7 bg-white">
            <div class="p-3">
                <div class="text-center">
                    <img src="{{ asset('assets/images/big/icon.png') }}" alt="wrapkit">
                </div>
                <h2 class="mt-3 text-center">Sign In</h2>
                <p class="text-center">Enter your email address and password to access admin panel.</p>
                <form class="mt-4" id="form" action="{{ route('signin') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="text-dark" for="email">Email</label>
                                <input class="form-control" name="email" id="name" type="text" placeholder="enter your email">
                                @error('email')
                                <div class="invalid-feedback" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="text-dark" for="pwd">Password</label>
                                <input class="form-control" name="password" id="pwd" type="password" placeholder="enter your password">
                                @error('password')
                                <div class="invalid-feedback" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="btn btn-block btn-dark">Sign In</button>
                        </div>
                        <div class="col-lg-12 text-center mt-5">
                            <a href="#" class="text-danger">Forget Password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection