<?php
$setting = DB::table('setting')->first();
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if( isset($setting->title)) {{ $setting->title }} @endif </title>
    <link rel="shortcut icon" href="{{ asset('assets/ico/'.@$setting->favicon) }}">
    <!-- CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <!-- Sweet Alert -->
    <link href="{{ asset('assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/plugins/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('admin_assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <link href="{{ asset('assets/css/icofont.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/owl.carousel.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/slidr.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/main.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css"/>
    <link id="preset" rel="stylesheet" href="{{ asset('assets/css/presets/preset'.$setting->theme_css.'.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css')}}">
    <!-- font -->
    <link href='https://fonts.googleapis.com/css?family=Ubuntu:400,500,700,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Signika+Negative:400,300,600,700' rel='stylesheet' type='text/css'>
    <!-- icons -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="{{asset('assets/js/cookie.js')}}"></script>
    <script>
        /* ajax post setup for csrf token */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var base_url = '<?= url('/') ?>';
        var currency = '{{$setting->currency}}';
    </script>
    <style>
        .nav-right .language-dropdown li>a{
            padding: 0;
        }
        .navbar-brand>img {
            height: 70px;
            margin-top: -22px;
        }
        .goog-te-gadget-icon{display: none}
        #header .navbar-default{
            border-radius: 0!important;
        }
         #footer, .footer-top{ background-color: {{ (isset($setting->footer_bg))? $setting->footer_bg : '' }}!important; }
        #main{ background-color: {{ isset($setting->body_bg)? $setting->body_bg : '' }}!important; }
        .footer-title{ color: {{ isset($setting->footer_head_color)? $setting->footer_head_color : '' }}!important; }
        #footer a, .copy-info{ color: {{ isset($setting->footer_link_color)? $setting->footer_link_color : '' }}!important; }
        @if(isset($setting->nav_bg))
        #header .navbar-default{
            background-color: {{ $setting->nav_bg }} !important;
        }
        @endif
        .goog-te-banner-frame, .goog-te-gadget-icon{
            display: none !important;
        }
        .goog-te-gadget-simple{border: 0!important; padding-top: 7px !important; background-color: transparent !important;}
        .goog-te-menu-value:hover{ text-decoration: none!important; }
       @if(!Auth::guest())
        @media (max-width: 767px){
            #header a.btn {
                top: 128px;
            }
        }
        @endif
        div.dropdown.language-dropdown li a i{
            display: block;
            text-align: center;
            font-size: 30px;
        }
        .dropdown.language-dropdown.open li a i{
            display: inline-block !important;
            font-size: 18px;
        }
        .kk li{padding: 5px}
        .goog-te-menu-value span:last-child{display: none}
    </style>
    {!! @$setting->header_script !!}
</head>
<body>
<input type="hidden" id="delete_link" value="{{url('/')}}/delete" >
<header id="header" class="clearfix">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <a href="{{url('/')}}" class="navbar-brand">
                    @if(isset($setting->logo)) <img src="{{ asset('assets/images/logo/'.$setting->logo)}}" alt="logo" class="img-responsive"> @endif
                </a>
            </div>
            <div class="nav-right">
                <div class="dropdown language-dropdown">
                    <ul class="sign-in">
                        <li><a href="{{url('search/query?main_category=mobile&custom_search%5Bcondition%5D%5B%5D=New')}}"> <i class="fa fa-mobile"></i> New Mobiles </a></li>
                        <span style="border-left: 1px solid rgb(187, 187, 187);margin: 0 5px 0 5px;">&#8203;</span>
                        <li><a href="{{url('search/query?main_category=mobile&custom_search%5Bcondition%5D%5B%5D=Used')}}"> <i class="fa fa-mobile"></i> Used Mobiles </a></li>
                        <span style="border-left: 1px solid rgb(187, 187, 187);margin: 0 5px 0 5px;">&#8203;</span>

                        <li><a href="{{url('showrooms')}}"> <i class="fa fa-building-o"></i> ShowRooms </a></li>
                        <span style="border-left: 1px solid rgb(187, 187, 187);margin: 0 5px 0 5px;">&#8203;</span>

                        <li><a href="{{route('active-auction')}}"><i class="fa fa-gavel"></i> Auctions</a></li>
                        <span style="border-left: 1px solid rgb(187, 187, 187);margin: 0 5px 0 5px;">&#8203;</span>

                @if(!Auth::guest())

                    @if($setting->translate == 1)
                        <li id="google_translate_element"></li>
                    @endif
                    @if($setting->map_listings == 1)
                        <li><a href="{{ route('location') }}"><i class="fa fa-map-marker"></i> Location</a></li><span style="border-left: 1px solid rgb(187, 187, 187);margin: 0 5px 0 5px;">&#8203;</span>
                    @endif
                    </ul>

                </div>
                <div class="dropdown language-dropdown">
                    <a data-toggle="dropdown" href="#"><span class="change-text">{{ ucfirst( Auth::user()->name ) }}</span> <i class="fa fa-angle-down"></i></a>
                    <ul class="dropdown-menu language-change kk">
                        @if(Auth::user()->type == 'adm')
                            <li><a href="{{ url('dashboard') }}"><i class="fa fa-bar-chart"></i> Admin Dashboard </a></li>
                        @endif
                        <li><a href="{{ url('user-panel') }}"><i class="fa fa-home"></i> Dashboard </a></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> Log out </a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </ul>

                </div>
            @endif
                <ul class="sign-in">
                    @if(Auth::guest())
                        @if($setting->translate == 1)
                            <li id="google_translate_element"></li>
                        @endif
                        @if($setting->map_listings == 1)
                            <li><a href="{{ route('location') }}"><i class="fa fa-map-marker"></i> Location</a></li>
                        @endif
                            <span style="border-left: 1px solid rgb(187, 187, 187);margin: 0 5px 0 5px;">&#8203;</span>
                        <li><a href="{{route('login')}}"><i class="fa fa-sign-in"></i> Sign In </a></li>
                    @endif
                </ul>
                <a href="{{route('ads.create')}}" class="btn">Post Your Ad</a>
            </div>
        </div>
    </nav>
</header>
@if(!auth()->guest())
    @if(auth()->user()->type == 'c')
        @if( auth()->user()->preview == 0 )
            <strong><p class="p-20 bg-danger m-b-0 text-info">Please add basic information about your company <a href="{{url('user-panel')}}">Dashboard</a> </p></strong>
        @elseif(auth()->user()->preview == 2)
            <strong><p class="p-20 bg-danger m-b-0 text-info">Your company profile is under review</p></strong>
        @endif
    @endif
@endif

@yield('content')
@include('partials.footer')