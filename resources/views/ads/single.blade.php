@extends('layouts.app')
@section('content')

    <style>
        .top-80{ top:80px}
        .list-group-item{  padding: 10px 5px}
        .btn-lg { width: 30%}
        .bx-viewport{ height: auto !important}
        .seller-info .thumbnail {height: 76px; width: 75px !important}
        .company-logo-thumb{float: left}

         h3.pull-left.title {
            font-size: 36px;
            font-weight: 400;
            color: #000;
            margin: 11px 0 8px;
        }
        #rating{
            margin-bottom: 5px;
        }
        #rating .fa{
            font-size: 22px!important;
        }
        .media-heading{ padding-left: 5px; font-size: 12px!important; }
        aside.panel.panel-body.panel-details{ padding:5px!important;}
        .key-features .media i {  font-size: 20px;   margin-right: 3px; }
        .text-center{ text-align: center!important; }
        .p-25{padding: 5px 15px 5px 15px;}
        .seller-info img{
            height:100px;
        }
        .col-md-5.bx-shadow{
            box-shadow: 2px 3px 17px -2px;
            padding-bottom: 20px;
        }
        .short-info{padding: 0px 10px 5px 10px;background-color: #f0f5f7;}

        #product-carousel .carousel-indicators {
            bottom: -140px;
        }
        .carousel-indicators {
            position: absolute;
            width: 100%;
            left: 0;
            bottom: -129px;
            margin-left: 0;
            border: none;
            text-align: left;
            right: 0;
            z-index: 15;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-pack: center;
            justify-content: center;
            padding-left: 0;
            margin-right: 15%;
            list-style: none;
        }
        .recommended-cta .banner-form-full{  margin: 0; }
        .recommended-cta .single-cta{ padding: 0; }
        #bid_amount{ width: 100% !important; }

        ::-webkit-input-placeholder { /* Edge */
            color: #60615f !important;
        }

        :-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: #60615f !important;
        }

        ::placeholder {
            color: #60615f !important;
        }
        .p-t-5{padding-top: 5px}
        .single-cta .banner-form{ border-radius: 0; background-color: rgba(173, 173, 173, 0.14);}
        .recommended-cta .cta, .short-info{background: #ededed;border: 1px solid #dddddd75;}
        .banner-form small { color: #676767; font-weight: 500; }
        #load_datatable_paginate .pagination>li>a{padding: 0px 6px;}
        #load_datatable_paginate .pagination>li>a:hover{ background: white!important; }
        #load_datatable tbody tr td:last-child{font-size: 10px}
    </style>
<?php
$setting = DB::table('setting')->first();
$avator = asset('assets/images/users/male.png');
if(!isset($data->user->image)){
    if($data->user->type == 'c' ){
        $avator = asset('assets/images/users/company.png');
    }
    if($data->user->type == 'u' || $data->user->type == 'adm' ){
        if($data->user->gender == 'm'){
            $avator = asset('assets/images/users/male.png');
        }
        if($data->user->gender == 'f'){
            $avator = asset('assets/images/users/female.png');
        }
    }
}else
    if($data->user->image!= ''){
        $avator = asset('assets/images/users/'.$data->user->image.'');
    }

?>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <section id="main" class="clearfix details-page">
        <div class="container">
            <div class="breadcrumb-section">
                <!-- breadcrumb -->
                <ol class="breadcrumb">
                    <li class="icon"><i class="fa fa-clock-o"></i><a href="javascript:void(0)" data-toggle="tooltip" data-title="Posted date"> {{ $data->created_at->diffForHumans() }} </a></li>
                    <li class="icon"><i class="fa fa-map-marker"></i><a href="javascript:void(0)" data-toggle="tooltip" data-title="city name"> {{ ucfirst($data->city->title) }} </a></li>
                    <li class="icon"><i class="fa fa-list"></i><a data-toggle="tooltip" data-title="category" href="{{url('search/query?main_category='.$data->category->slug)}}"> {{ ucfirst($data->category->name) }} </a></li>
                    <li class="icon"><i class="fa fa-line-chart"></i><a href="javascript:void(0)" data-toggle="tooltip" data-title="ad views"> {{ floor($data->visit/2) }} </a></li>
                </ol><!-- breadcrumb -->
                <h2 class="title">{{ ucfirst($data->category->name) }}</h2>
            </div>

            <div class="section slider">
                <div class="row">
                    <!-- carousel -->
                    <div class="col-lg-7">
                        <div id="product-carousel" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">

                                @if(count($data->ad_images) > 0)
                                    <?php $count=0; ?>
                                    @foreach($data->ad_images as $img)
                                        <li data-target="#product-carousel" data-slide-to="{{$count}}" class="@if($count == 0) active @endif ">
                                            <img class="img-fluid" src="{{ asset('assets/images/listings/'.$img->image.'') }}" alt="img">
                                        </li>
                                        <?php $count++; ?>
                                    @endforeach
                                @else
                                    <li style="float: left; list-style: none; position: relative; width: 497px;" class="img-fluid">
                                        <img src="{{ asset('assets/images/listings/empty.jpg') }}" alt="img"></li>
                                @endif
                            </ol>

                            <!-- Wrapper for slides -->
                            <div class="carousel-inner" role="listbox">


                                <!-- item -->
                                @if(count($data->ad_images) > 0)
                                    <?php $count=0; ?>
                                    @foreach($data->ad_images as $img)
                                        <div class="item carousel-item @if($count == 0) active @endif">
                                            <div class="carousel-image">
                                                <!-- image-wrapper -->
                                                <img src="{{ asset('assets/images/listings/'.$img->image.'') }}" alt="Featured Image" class="img-fluid">
                                            </div>
                                        </div><!-- item -->
                                        <?php $count++; ?>
                                    @endforeach
                                @else
                                    <div class="item carousel-item @if($count == 0) active @endif">
                                        <div class="carousel-image">
                                            <!-- image-wrapper -->
                                            <img src="{{ asset('assets/images/listings/empty.jpg') }}" alt="Featured Image" class="img-fluid">
                                        </div>
                                    </div><!-- item -->
                                @endif
                            </div><!-- carousel-inner -->

                            <!-- Controls -->
                            @if(count($data->ad_images) > 0)
                            <a class="left carousel-control" href="#product-carousel" role="button" data-slide="prev">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                            <a class="right carousel-control" href="#product-carousel" role="button" data-slide="next">
                                <i class="fa fa-chevron-right"></i>
                            </a><!-- Controls -->
                            @endif
                        </div>
                    </div><!-- Controls -->

                    <!-- slider-text -->
                    <div class="col-lg-5">
                        <div class="slider-text">

                            <h2>{{ $setting->currency_place == 'left' ? $setting->currency : ''  }}{{ number_format($data->price) }} {{  $setting->currency_place == 'right' ? $setting->currency : ''  }}  {{ ($data->price_option!='')? '/'.$data->price_option : '' }}</h2>
                            <h3 class="title">{{ ucfirst($data->title) }}</h3>

                            <!-- short-info -->
                            <hr>
                            <div class="short-info row">
                                    <h4>{{ ( $data->user->type == 'c' )? 'Contact Dealer' : 'Contact Person' }}</h4>
                                    <div class="seller-info" style="position: relative">
                                        <div class="company-logo-thumb col-md-4 row">
                                            <img src="{{ $avator }}" class="thumbnail" alt="img">
                                        </div>
                                        <div class="company-logo-thumb col-md-8 row">
                                            <div class="m-l-5">
                                                <h4 class="no-margin"> {{ ucwords($data->user->name) }}   <i> {!!  ($data->user->is_login == 1)? '<span data-toggle="tooltip" data-title="Online" class="fa online"></span>' : '<span data-toggle="tooltip" data-title="Offline" class="fa offline"></span>' !!} </i></h4>
                                                @if($data->user->is_verified == 2)
                                                    <a class="label label-primary text-white"> Verified User </a>
                                                @endif
                                                @if($data->user->mobile_verify == 1)
                                                    <a class="label label-primary m-l-5 text-white m-t-5">Phone Verified</a>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="text-leftt clearfix">

                                            @if($data->user->type == 'c')
                                                @if($data->user->telephone)
                                                    <p>Telephone: <strong>{{ ucfirst($data->user->telephone) }}</strong></p>
                                                @endif
                                                @if($data->user->fax)
                                                    <p>FAx: <strong>{{ ucfirst($data->user->fax) }}</strong></p>
                                                @endif
                                                @if($data->user->address)
                                                    <p>Location: <strong>{{ ucfirst($data->user->address) }}</strong></p>
                                                @endif
                                            @endif

                                            <?php

                                            $user_rated = '';
                                            $rating = DB::table('user_rating')
                                                ->select(DB::raw('count(score) as total, AVG(score) as avg'))
                                                ->where('user_id', $data->user->id)->first();

                                            if(!Auth::guest()){
                                                /* check ip recorded rating ? */
                                                $user_rated = DB::table('user_rating')
                                                    ->where(['user_id' => $data->user->id, 'by_user' => auth::user()->id])
                                                    ->value('id');
                                            }
                                            ?>
                                            @if(!Auth::guest() && auth::user()->id != $data->user->id)
                                                <div data-toggle="tooltip" id="rating" {{ $user_rated!='' ? 'data-read="true"' : '' }} data-score="{{ floor($rating->avg) }}" class="click" ></div>
                                            @else
                                                <div id="rating" data-read="true" data-score="{{ floor($rating->avg) }}" class="click" ></div>
                                            @endif

                                        </div>
                                        <a href="{{ url('user-ads', $data->user->id) }}" class="btn btn-primary btn-block m-t-5"> <i class=" fa fa-user"></i> More ads by User </a>
                                    </div>
                                </div>


                            <!-- contact-with -->
                            <div class="contact-with">
                                <h4>Contact with </h4>
                                <span class="btn btn-red show-number">
									<i class="fa fa-phone-square"></i>
									<span class="hide-text">Click to show phone number </span>
									<span class="hide-number">{{ $data->user->phone}}</span>
								</span>
                                @if( $data->user->id != Auth::user()->id)
                                    <a href="javascript:;" class="btn" data-toggle="modal" data-target="#contactModalId"><i class="fa fa-envelope-square"></i>Reply by email</a>
                                @endif
                                @if(!Auth::guest() && Auth::user()->chat_lock == 0 && $setting->live_chat==1)
                                <!-- check if user is locked or not in chat setting table -->
                                    <?php
                                    $is_locked = DB::table('chat_setting')->where([ 'user_id' => Auth::user()->id, 'blocked_user' => $data->user->id ] )->value('blocked_user');

                                    ?>
                                    @if( $data->user->id != Auth::user()->id && $data->user->is_login == 1 && $is_locked =='' )
                                        <button  class="btn btn-default btn-lg chathead" data-for="{{ $data->user->id }}"><i class=" icon-chat"></i> Chat Now </button>
                                    @endif
                                @endif
                            </div><!-- contact-with -->

                        </div>
                    </div><!-- slider-text -->
                </div>
            </div><!-- slider -->

            <div class="description-info">
                <div class="row">
                    <!-- description -->
                    <div class="col-md-8">
                        <div class="description">
                            <h4>Description</h4>
                            {!! $data->description !!}
                            <hr>
                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id)) return;
                                    js = d.createElement(s); js.id = id;
                                    js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2';
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));
                            </script>
                            <div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5"></div>
                        </div>
                    </div><!-- description -->

                    <!-- description-short-info -->
                    <div class="col-md-4">
                        <?php $auction = \App\Auction::where('ad_id', $data->id)->where('status', 1)->first(); ?>
                        @if( $auction)

                        <?php
                            $timer=false;
                            $bidHis = \App\UserBids::where('auction_id', $auction->id)->count();
                            $max_bid =  \App\UserBids::where('auction_id',$auction->id )->max('amount');

                            $auction_hour = $auction->hour;

                            if($auction_hour!=''){
                                $created = $auction->created_at->addHours($auction_hour);
                                $now = \Carbon\Carbon::now('UTC');

                                if($created > $now){
                                    $timer = true;
                                }

                                $timeLeft = $created->format('D H:i');

                                $sec = $created->diffInSeconds($now);

                                $days=$hour=$min='';

                                if($created->diffInDays($now)>0){
                                    $days = $created->diffInDays($now).'d ';
                                }
                                if(gmdate('H', $sec) > '00'){
                                    $hour = gmdate('H', $sec).'h ';
                                }
                                $min = gmdate('i:s', $sec);
                                $time = $days.ltrim($hour, 0).$min;
                            }
                        ?>
                            @if($timer)

                        <link href="https://fonts.googleapis.com/css?family=Orbitron&display=swap" rel="stylesheet">
                        <div class="recommended-cta">
                            <div class="cta">
                                <!-- single-cta -->
                                <div class="single-cta p-0">
                                    <!-- cta-icon -->
                                    <div class="cta-icon icon-secure text-center">
                                        <i class="fa fa-gavel fa-5x"></i>
                                        <h4 class="text-info" id="timer"><small>Time Left: </small> <span style="font-family: 'Orbitron', sans-serif;"> {{ $time  }} </span><small>{{$timeLeft}}</small> </h4>
                                    </div><!-- cta-icon -->
                                </div>
                                <!-- single-cta -->
                                <div class="single-cta">
                                    <div class="banner-form">
                                        <div class="m-b-5">
                                            <small class="pull-left"> Minimum Bid price {{ $setting->currency_place == 'left' ? $setting->currency : ''  }}{{ number_format($data->price) }} {{  $setting->currency_place == 'right' ? $setting->currency : ''  }}  {{ ($data->price_option!='')? '/'.$data->price_option : '' }}</small>
                                            <small class="pull-right"> Top Max bid <span class="text-danger">{{ $setting->currency_place == 'left' ? $setting->currency : ''  }}<span class="max_bid">{{ $max_bid==''? 0 : $max_bid }}</span>{{  $setting->currency_place == 'right' ? $setting->currency : ''  }}</span> </small>
                                            <div class="clearfix"></div>
                                        </div>
                                        <form id="bid_form" autocomplete="off">
                                            <input type="hidden" value="{{ $auction->id }}" name="auction_id">
                                            <input maxlength="11" id="bid_amount" type="text" name="amount" required class="form-control" placeholder="Enter bid amount" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
                                            <button type="submit" class="form-control">Add Bid</button>
                                        </form>
                                        <div class="p-t-5">
                                            <a href="javascript:void(0)" class="p-l-10 pull-left">Total bids <span class="badge bid_history">{{$bidHis}}</span></a>
                                            @if( $auction->description !='' )
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#bid_description" class="p-l-10 pull-right">Bid description</a>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="p-l-r-10">
                                        <h5>Bids History <a href="javascript:void(0)" onclick="$('#load_datatable').DataTable().ajax.reload();" class="pull-right"><i class="fa fa-refresh"></i></a> </h5>
                                        <div class="table-responsive">
                                            <table id="load_datatable" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable dataTable no-footer">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>date</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div><!-- single-cta -->
                            </div>
                        </div>
                            <!-- Modal show phone number -->
                            <div class="modal fade top-80" id="bid_description" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h3 class="modal-title text-center">  Description</h3>
                                        </div>
                                        <div class="modal-body">
                                            {!! $auction->description !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>

                                var win = false;

                                setInterval(function(){
                                    $('#load_datatable_processing').addClass('hidden')
                                    $.post("{{url('get_bids_data')}}",{ id: '{{$auction->id}}' }, function (result) {
                                        if(result.success == true){
                                            $('.badge.bid_history').text(result.history);
                                        }
                                        if(result.hasOwnProperty('max_bid')){
                                            $('.max_bid').text(result.max_bid!=null?result.max_bid:0);
                                        }
                                        $('#load_datatable').DataTable().ajax.reload();
                                    })
                                },5000);

                                /*var d = Number(558);
                                var duration =  d;
                                var timer = 1;
                                var countdown = duration *60*1000;
                                var ctn =  1000;
                                if(duration>0){

                                    if(typeof($.cookie('sec'))!='undefined'){
                                         var passedtime = Number($.cookie('time'));

                                        if(!isNaN($.cookie('sec'))){

                                            if( $.cookie('_id') != '{{$auction->ad_id}}' ){
                                                $.cookie('_id','{{$auction->ad_id}}');
                                                $.cookie('sec','1000');
                                            }else {
                                                countdown = countdown - Number($.cookie('sec'));
                                                ctn = Number($.cookie('sec'));
                                            }
                                        }
                                        //alert(countdown);
                                    }else{
                                        $.cookie('sec','1000');
                                        $.cookie('_id','{{$auction->ad_id}}');
                                    }
                                    var timerId = setInterval(function(){
                                        countdown -= 1000;
                                        ctn += 1000;
                                        var mint = Math.floor(countdown / (60 * 1000));
                                        var hour = Math.floor(mint / 60);
                                        hour = hour%24;
                                        var min = mint % 60;
                                        var sec = Math.floor((countdown - (mint*60*1000)) / 1000);  //correct
                                        var day = Math.floor(mint/1440); // 60*24
                                        $.cookie('sec', ctn);
                                        if(countdown<=0){
                                            if(win == false){
                                                $.post('{{url('auction_expired')}}',
                                                    { id: '{{$auction->id}}' },
                                                    function (res) {
                                                        if(res.success == true){
                                                        @if(!Auth::guest())
                                                            var auth_user = '{{auth()->user()->id}}';
                                                            if( auth_user == res.user_id ){
                                                                swal('You have won the bid');
                                                                $('#timer').remove();
                                                            }
                                                            @else
                                                            swal('Auction time expired');
                                                            $('#timer').remove();
                                                        @endif
                                                        }
                                                    });
                                                win = true
                                            }
                                        }else {
                                            if (hour < 1) {
                                                var hour = '0';
                                            }
                                            if (day==1) {
                                                day = day + " day ";
                                            }else
                                                if(day > 1){
                                                    day = day + " days ";
                                            }else {
                                                day = "";
                                            }
                                            $('#timer').html(day + hour + ":" + min + ":" + sec);
                                        }
                                    },1000);
                                }else{
                                    window.location.href='';
                                }
                                console.log($.cookie('sec'));*/
                            </script>

                                @else
                                <script>
                                    $.removeCookie("sec");
                                    $.removeCookie("_id");
                                </script>
                            @endif
                        @endif
                        <div class="short-info">
                            <!-- social-links -->
                            <div class="social-links">
                                <h4>Share this ad</h4>
                                <div class="a2a_kit a2a_kit_size_32 a2a_default_style m-b-5 m-t-20" style="margin-top: -18px;">
                                    <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                    <a class="a2a_button_sms"></a>
                                    <a class="a2a_button_facebook"></a>
                                    <a class="a2a_button_twitter"></a>
                                    <a class="a2a_button_whatsapp"></a>
                                    <a class="a2a_button_copy_link"></a>
                                    <a class="a2a_button_facebook_messenger"></a>
                                    <a class="a2a_button_google_plus"></a>
                                </div>
                                <script async src="https://static.addtoany.com/menu/page.js"></script>
                            </div><!-- social-links -->
                            <hr>

                            @if(count($ad_cf_data) > 0 )
                                    <h4>Short Info</h4>
                                    <ul>
                                        @foreach($ad_cf_data as $index => $value)
                                            <?php $type = $value->type ?>
                                            @if($value->column_value !='' && $value->img =='' && $value->type !='textarea')
                                                @if($value->icon !='' || $value->image!='')
                                                    <li> <strong> {!! ($value->icon!='')? '<i class="'.$value->icon.'"></i>' : '<img src="'.asset('assets/images/c_icons/'.$value->image.'').'" height="20" width="20">' !!}</strong> </li>
                                                @endif
                                                <li><strong>{{ ucfirst( str_replace('_', ' ', $value->column_name) ) }} </strong>
                                                    @if( $type == 'url')
                                                        <strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pull-right" target="_blank" href="{{ $value->column_value  }}"> {{  parse_url($value->column_value, PHP_URL_HOST)  }}</a></strong>
                                                    @elseif( $type =='checkbox' || $type == 'radio')
                                                        {{ (ucfirst($value->column_value) == 1)? 'Yes': ucfirst($value->column_value) }} {{ $value->inscription }}
                                                    @else
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    {{ ucfirst($value->column_value) }} {{ $value->inscription }}
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- download -->
    <section id="something-sell" class="clearfix parallax-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h2 class="title">Do you have something-sell?</h2>
                    <h4>Post your ad for free on Trade.com</h4>`
                    <a href="{{ route('ads.create') }}" class="btn btn-primary">Post Your Ad</a>
                </div>
            </div><!-- row -->
        </div><!-- contaioner -->
    </section><!-- download -->


<!-- Modal contact user-->
<div class="modal fade top-80" id="contactModalId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="loginModelTitleId"> {{ (!Auth::guest())? 'Contact User' : 'Login to send message' }} </h4>
            </div>
            <div class="modal-body">
                <div id="login-container" class="{{ (!Auth::guest())? 'hidden' : '' }}">
                    <form id="loginform">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <div class="col-md-12">
                                <label for="email" class=" control-label">E-Mail Address:</label>
                                <div class="input-icon"><i class="icon-user fa"></i>
                                    <input id="email" type="email" class="form-control" name="email"  required autofocus autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label for="password" class="control-label">Password:</label>
                                <div class="input-icon"><i class="icon-lock fa"></i>
                                    <input id="password" type="password" class="form-control" name="password" required autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="">
                                    <label>
                                        <input type="checkbox" name="remember" > Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 ">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>
                <div id="Email-container" class="{{ (Auth::guest())? 'hidden' : '' }}">
                <div id="Email-container" class="{{ (Auth::guest())? 'hidden' : '' }}">
                    <form id="contactForm" action="">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $data->user->id }}">
                        <input type="hidden" name="ad_id" value="{{ $data->id }}">
                        <div class="form-group">
                            <label for="">Message <span class="text-danger">*</span></label>
                            <textarea required class="form-control" name="msg" id="" rows="3" placeholder="Add text here.."></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success"> <i class="icon-mail" aria-hidden="true"></i> Send </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                @if(Auth::guest())
                    <a href="{{ route('register') }}" class="btn btn-primary reg_btn">Register</a>
                @endif
                <a href="javascript:void(0)" type="button" class="btn btn-info" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Modal show phone number -->
    <div class="modal fade top-80" id="showPhone" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title text-center">  Do business in safety on {{ $setting->title }}</h3>
                </div>
                <div class="modal-body text-center">
                    <div class="p-25">
                        <p>
                            If possible meet the seller in person to close the deal. Try to avoid untraceable payment tools.
                        </p>
                        <h3><i class="fa fa-phone" aria-hidden="true"></i> @if($data->user->phone == '') <span class="text-danger">Not available!</span>
                            @else
                                {{ $data->user->phone }}
                                @if($data->user->mobile_verify == 1)
                                    <div class="btn btn-xs btn-success ">Phone Verified</div>
                                @endif
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="detail" class="hidden">
        {{ strip_tags(trim(rtrim($data->description))) }}
    </div>

<script src="{{asset('assets/plugins/bxslider/jquery.bxslider.min.js')}}"></script>

    <!-- Rating js -->
    <script src="{{asset('admin_assets/plugins/raty-fa/jquery.raty-fa.js')}}"></script>
<script>
    // detect mobile user
    var is_mobile = !!navigator.userAgent.match(/iphone|android|blackberry/ig) || false;

    $(document).ready(function () {

        $('#bid_form').submit(function () {

            @if( Auth::guest() )
                swal('Oops!', 'Please Login to bid!', 'warning');
                return false;
            @endif

            $('#loading').show();
            var data = new FormData(this);
            $.ajax({
                url: "{{route('save-bid')}}",
                data: data,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(result){
                    if(result.success == true){
                        swal('Success', result.message, 'success');
                        $('.badge.bid_history').text(result.history);
                    }else{
                        swal('Error!', result.message, 'error');
                    }

                    if(result.hasOwnProperty('max_bid')){
                        $('.max_bid').text(result.max_bid!=null?result.max_bid:0);
                    }

                    $('#loading').hide();
                    $('#load_datatable').DataTable().ajax.reload();
                }
            });

            return false;
        });


        $('#load_datatable').DataTable({
            "pageLength":5,
            //"order": [[0, 'desc']],
            processing: true,
            serverSide: true,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            bFilter: false, bInfo: false,
            "initComplete": function (settings, json) {
            },
            ajax: "{!! url('get-bid-history') !!}?id={{$data->id}}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'amount', name: 'amount'},
                {data: 'time', name: 'time', searchable:false, orderable: false },
            ]
        });

        $('#load_datatable_paginate').parent().removeClass('col-sm-6').addClass('col-sm-12');


        var detail = $('#detail').html();

        $('head').append('<meta property="og:url"   content="{{url()->full()}}" />' +
            '  <meta property="og:type"          content="website" />' +
            '  <meta property="og:title"         content="{{ ucfirst($data->title) }}" />' +
            '  <meta property="og:description"   content="'+detail+'" />' +
            '  <meta property="og:image"         content="@if(isset($data->ad_images[0]->orignal_filename)){{ asset('assets/images/listings/'.$data->ad_images[0]->image) }}@endif" /><!-- Twitter Card data -->\n' +
            '<meta name="twitter:card" content="'+detail+'">\n' +
            '<meta name="twitter:site" content="@<?php echo url('/') ?>">\n' +
            '<meta name="twitter:title" content="{{ ucfirst($data->title) }}">\n' +
            '<-- Twitter Summary card images must be at least 120x120px -->\n' +
            '<meta name="twitter:image" content="@if(isset($data->ad_images[0]->orignal_filename)){{ asset('assets/images/listings/'.$data->ad_images[0]->image) }}@endif">');

        if(is_mobile) {
            $('#Mobile_Contact').attr('data-toggle', '').attr('data-target', '');
            $('#Mobile_Contact').attr('href','tel:'+ $('#phone_show').html());
                $('#phone_hide,#phone_show').text('Call');
                $('.chathead').html('<i class=" icon-chat"></i> Chat');
        }
        // ajax submit form
        $("#loginform").submit(function(){
            $('#loading').show();
            var data = new FormData(this);
            $.ajax({
                url: "{{url('user-login')}}",
                data: data,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(result){
                    if(result!=''){
                        //var obj = $.parseJSON(result);
                        var obj = result;
                        if(obj.msg == 1) {
                            swal('Success', 'Login successfully!', 'success');
                            $('#loginModelTitleId').html('Contact User');
                            $('#login-container, .reg_btn').addClass('hidden');
                            $('#Email-container').removeClass('hidden');
                        }else{
                            swal('Error!', 'Wrong password provided!', 'error');
                        }
                        $('#loading').hide();
                    }
                }
            });
            return false;
        });

        // contact Form
        $("#contactForm").submit(function(){
            $("#contactForm button").html( '<i class="fa fa-spinner fa-spin"></i> processing..' );

            var data = new FormData(this);
            $.ajax({
                url: "{{url('contact-user')}}",

                data: data,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(result){
                    if(result!=''){

                        if(result == 1) {
                            $('#contactModalId').modal('hide');
                            swal('Success', 'Message send successfully!', 'success');
                            window.location.reload();
                        }else{
                            swal('Error!', 'Unknown error!', 'error');
                        }
                        $("#contactForm button").html( 'Send' );
                        $("#contactForm textarea").html( '' );
                    }
                }
            });
            return false;
        });
    });
    // start chat function

    $('.chathead').on('click', function () {

        var forchat = $(this).data('for');
        $('#target').val(forchat);

         $.cookie("chatHead", '{{ $data->user->id }}', { path: '/' });
         $.cookie("chatUser", '{{ ucwords($data->user->name) }}', { path: '/' });

        var to = $.cookie('chatHead');

        if(hide_chat != 'hide') {
            $.post(
                    '{{route('load_chat_head')}}',
                    {id: to},
                    function (data) {
                        console.log(data.length);
                        if (data != 0) {
                            //$('#chat .chat').html(data);
                            // scroll down
                            $("#chat .panel-body").scrollTop($("#chat .panel-body")[0].scrollHeight);
                            $('.badge-white').html('0');
                        } else {

                        }
                    }
            );
            $('#chat .input-group').removeClass('hidden');
        }

        if($('#chat .panel-body').hasClass('hidden')){
            if(hide_chat != 'hide') {
                $('#chat .chat').find('center').html('<center> There are not any chats yet! <br> Start chat with <strong>' + $.cookie('chatUser') + '</strong> </center>');
            }
            $('#chat .panel-body,#chat .panel-footer, #chat .btn-settings ').removeClass('hidden');
            $('#chat .btn-indicator').html('<i class="fa fa-angle-up"></i>');
            $("#chat .panel-body").scrollTop($("#chat .panel-body")[0].scrollHeight);
        }else{
            $('#chat .panel-body,.panel-footer').addClass('hidden');
            $('#chat .btn-indicator').html('<i class="fa fa-angle-down"></i>');
        }
    });
    // end chat function

        $('.bxslider').bxSlider({
            pagerCustom: '#bx-pager',
            adaptiveHeight: true
        });

        function show_hide(e){
            var id = $(e).attr('id');
            var str = $(e).data('str');
            var iff = $(e).attr('data-if');

            if(iff == 0){
                $(e).html('<a href="javascript:void(0)"> Hide ' + str + '</a>' );
                $(e).attr('data-if', "1");
            }else {
                $(e).attr('data-if', '0');
                $(e).html( '<a href="javascript:void(0)"> Show all ' + str + '</a>' );
            }
            $('.'+id).toggleClass('hidden', 'slow');

        }

    /* ratery  */

    var hint = '{{$rating->total}} votes, average {{ floor($rating->avg) }} out of 5';

    (function ($) {
        $(function () {
            $('#readOnly').raty({
                readOnly: true,
                score: 3,
            });

            $('.click').raty({
                readOnly: function(){
                    return $(this).attr('data-read');
                },
                score: function () {
                    return $(this).attr('data-score');
                },
                click: function (score, evt)
                {
                    $.post('{{ route('user-rating') }}',
                        {user_id:'{{ $data->user->id }}', score:score, ip:'{{ $user_ip }}'},
                            function(data){
                                if(data.msg == 1){
                                    $("#rating").html('<span class="text-success">Rated '+score+' successfully!</span>');
                                }
                            });
                },
                half: true,
                hints: [hint, hint, hint, hint, hint]
            });
        });
    })(jQuery);
</script>
</div>
@endsection
