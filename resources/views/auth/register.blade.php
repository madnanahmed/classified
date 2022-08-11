@extends('layouts.app')
@section('content')

<?php  $setting = DB::table('setting')->first(); ?>

<style>
    .text-danger{
        color: #a94442 !important;
        text-align: left;
    }
</style>
<section id="main" class="clearfix user-page">
    <div class="container">
        <div class="row">
            <!-- user-login -->
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <div class="user-account">
                    <h2 class="text-center">Create Account</h2>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('register') }}" method="post">
                        {{csrf_field()}}
                        <div class="form-group">
                            <input id="name" type="text" class="form-control" placeholder="User name" name="name" value="{{ old('name') }}" required autofocus autocomplete="off">
                        </div>

                        @if ($errors->has('name'))
                            <span class="help-block text-danger">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                        <div class="form-group">
                            <input id="email" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="off">
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block text-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif

                        <select id="type" type="type" class="form-control" name="type" value="{{ old('type') }}" required autocomplete="off">
                            <option value="">Select user type</option>
                            <option {{ (old('type') == 'u')? 'selected' : '' }} value="u">Private user</option>
                            <option {{ (old('type') == 'c')? 'selected' : '' }} value="c">ShowRoom</option>
                        </select>

                        @if ($errors->has('type'))
                            <span class="help-block text-danger">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif

                        <div class="form-group">
                            <input id="password" type="password" placeholder="Password" class="form-control" name="password" required autocomplete="off">
                        </div>

                        @if ($errors->has('password'))
                            <span class="help-block text-danger">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                        @endif
                        <div class="form-group">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                        </div>
                        <!-- select -->
                        <div class="row form-group adpost-details">
                            <label class="col-sm-3">Gander:</label>
                            <div class="col-sm-9 user-type">
                                <input type="radio" name="gender" value="m" {{ (old('gender') == 'm')? 'checked' : '' }} id="newsell"> <label for="newsell">Male </label>
                                <input type="radio" name="gender" value="f" {{ (old('gender') == 'f')? 'checked' : '' }} id="newbuy"> <label for="newbuy">Female</label>
                            </div>
                        </div>

                        <div class=" text-center">
                            <button type="submit" href="#" class="btn btn-block">Sign Up</button>
                        </div>

                        <div class="text-center">
                            <h4>OR</h4>
                        </div>

                        <div class="contact-with text-center">
                            <a href="{{url('login/facebook')}}">
                                <img class="img" src="{{asset('assets/images/facebook.png')}}"  alt="">
                            </a>
                            <br>
                            <br>
                            <a href="{{url('login/google')}}" class="m-t-15">
                                <img class="img" src="{{asset('assets/images/google.png')}}"  alt="">
                            </a>
                        </div>

                    </form>
                    <!-- checkbox -->
                </div>
            </div><!-- user-login -->
        </div><!-- row -->
    </div><!-- container -->
</section><!-- signup-page -->

@endsection
