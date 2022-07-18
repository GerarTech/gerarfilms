@extends('layouts.app')

@section('content')



    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{url('/api/image/logo')}}" alt="logo">
                            </div>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input class="form-control form-control-lg" placeholder="E-mail" name="email"
                                           value="{{ old('email') }}" id="email" type="text">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" placeholder="*********"
                                           name="password" required autocomplete="current-password" id="password">
                                </div>
                                <div class="mt-3">
                                    <button
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">{{ __('Login') }}</button>
                                </div>
                            </form>


                            <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input">
                      Keep me signed in
                    <i class="input-helper"></i></label>
                  </div>
                  <a href="{{ route('password.request') }}" class="auth-link text-black">Forgot password?</a>
                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>



@endsection
