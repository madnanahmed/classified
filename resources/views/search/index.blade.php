@extends('layouts.app')
@section('content')
    <style>
        .price-box img{height: 20px!important; }
        .irs-single{ margin-left: 9px; }
        .btn-auths{ padding: 2px 7px !important; }
        .ads-details .btn{ white-space: normal;  }
        input[type='text'].form-control{ padding: 5px 5px }
        .form-control{border: 1px solid #c0c0c0;}

        #cf_field .col-md-2{
            padding: 0 1px!important;
        }
        #cf_field .col-md-2:first-child{
            padding-left: 5px!important;
        }
        .listings-item.row{position: relative}
        .timex{
            position: absolute;
            right: 0;
            background: black;
            color: wheat;
            font-size: 12px;
            z-index: 99;
        }
        .banner-form-full.banner-form input {
            min-width: auto
        }
        .algolia-places{width: inherit !important;display: inline !important;}
        #address-input{min-width: auto;}
        .banner-form .form-control, .banner-form .category-dropdown{
            width: 30%;
            border-left: 1px solid #e6e6e6;
            border-radius: 0;
        }
        #keyword{border-right: 1px solid #e6e6e6!important;}
    </style>
    <!-- ION Slider -->
    <link href="{{ asset('assets/plugins/ion-rangeslider/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/ion-rangeslider/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" type="text/css"/>
    <script>
        function countDown (user_time, elem) {
            var d = Number(user_time);
            var duration =  d;
            var timer = 1;
            var countdown = duration *60*1000;
            var ctn =  1000;
            if(duration>0){
                var timerId = setInterval(function(){
                    countdown -= 1000;
                    ctn += 1000;
                    var mint = Math.floor(countdown / (60 * 1000));
                    var hour = Math.floor(mint / 60);
                    hour = hour%24;
                    var min = mint % 60;
                    var sec = Math.floor((countdown - (mint*60*1000)) / 1000);  //correct
                    var day = Math.floor(mint/1440); // 60*24

                    if(countdown<=0){


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
                        $(elem).html(day + hour + ":" + min + ":" + sec);
                    }

                },1000);
            }else{
                window.location.href='';
            }
        }
    </script>
    <?php
    $setting = DB::table('setting')->first();
    $req_city = $req_keyword = $req_price_sort = $low_price = $high_price = $online= '';

    if(isset($_REQUEST['online']))
        $online = $_REQUEST['online'];
    if(isset($_REQUEST['city']))
        $req_city = $_REQUEST['city'];
    if(isset($_REQUEST['keyword']))
        $req_keyword = $_REQUEST['keyword'];
    if(isset($_REQUEST['price_sort']))
        $req_price_sort = $_REQUEST['price_sort'];
    if(isset($_REQUEST['price_range'])){
        $price_exp = explode(';', $_REQUEST['price_range']);
        $low_price = $price_exp[0];
        $high_price = $price_exp[1];
    }else{
        $low_price = 1;
        $high_price = 10000000;
    }
    $image = 1;
    if(isset($_REQUEST['image']))
        $image = $_REQUEST['image'];
    ?>
    <?php

    function buildCategory($parent, $category) {
        //print_r($category['parent_cats'][$parent]);
        $html = $bold = "";
        if (isset($category['parent_cats'][$parent])) {
            $html .= "";
            foreach ($category['parent_cats'][$parent] as $cat_id) {
                if (!isset($category['parent_cats'][$cat_id])) {
                    $html .= "<option value='".$cat_id."'>" .ucfirst($category['categories'][$cat_id]['name']). "</option>";
                }
                if (isset($category['parent_cats'][$cat_id])) {
                    if($category['categories'][$cat_id]['parent_id'] == 0){
                        $html .= "<optgroup label='" . ucfirst($category['categories'][$cat_id]['name']) . "'>";
                    }else{
                        $html .= "<option disabled value='".$cat_id."'> " .ucfirst($category['categories'][$cat_id]['name']). "</option>";
                    }
                    //$html .= "<optgroup label='" . ucfirst($category['categories'][$cat_id]['name']) . "'>";
                    $html .= buildCategory($cat_id, $category);
                    $html .= "</optgroup>";
                }
            }
            $html .= "";
        }
        $html .='';
        return $html;
    }
    ?>
    <section id="main" class="clearfix home-default">
        <div class="container">
            <div class="search-row-wrapper">
                @if($setting->search_ads  == 1 && $setting->search_ads_p == 'bs' )
                    <style>.banner-form-full {margin-top: 0!important;}</style>
                    <div class="ads_bs m-b-10">
                        {!! $setting->search_adsense !!}
                    </div>
                @endif

                <div class="banner-form banner-form-full">
                    <form id="search_form" action="{{url('search/query')}}" method="get">
                        @if(isset($_REQUEST['main_category']))
                            <input type="hidden" name="main_category" value="{{ $_REQUEST['main_category'] }}">
                        @endif
                            <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $req_keyword }}" placeholder="Type Your key word">
                            <input type="text" id="address-input" value="{{$req_city}}" class="form-control locinput input-rel" placeholder="City">

                            <input type="hidden" id="truecity" name="city" value="{{$req_city}}" style="display:none;">
                            <input type="hidden" id="countryCode" name="country" style="display:none;"/>
                        <!-- language-dropdown -->
                        <div class=" category-dropdown">
                            <select name="category" class=" locinput input-rel" id="category">
                                <option value=""> Select Category</option>
                                {!! buildCategory(0, $category) !!}
                                {{--<input class="form-control" id="category" name="category" readonly type="text" data-toggle="modal" data-target="#categoryModal">--}}
                            </select>
                        </div><!-- language-dropdown -->
                        <input id="price_sort" type="hidden" name="price_sort">

                        <button type="submit" class="form-control" value="Search">Search</button>
                    </form>
                </div>
                    @if($setting->search_ads  == 1 && $setting->search_ads_p == 'as' )
                        <div class="ads_bs m-b-10">
                            {!! $setting->search_adsense !!}
                        </div>
                    @endif
            </div>
            <div class="category-info">
                <div class="row">
                    <!-- accordion-->
                    <div class="col-sm-4 col-md-3">
                        <div class="accordion">
                            <!-- panel-group -->
                            <div class="panel-group" id="accordion">
                                <!-- panel -->
                                <div class="panel-default panel-faq">
                                    <!-- panel-heading -->
                                    <div class="panel-heading">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#accordion-one">
                                            <h4 class="panel-title">All Categories<span class="pull-right"><i class="fa fa-minus"></i></span></h4>
                                        </a>
                                    </div><!-- panel-heading -->

                                    <div id="accordion-one" class="panel-collapse collapse in">
                                        <!-- panel-body -->
                                        <div class="panel-body">
                                            <ul>
                                                @foreach(\App\Category::select('name','slug','icon', 'image')->where(['parent_id'=>0, 'status' => 1 ])->get() as $cat )
                                                    <li @if(str_replace('-', ' ', @$_REQUEST['main_category'] ) == strtolower($cat->name)) class="active" @endif ><a href="{{url('search/query?main_category='.urlencode($cat->slug))}}">{{ ucwords($cat->name) }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div><!-- panel-body -->
                                    </div>
                                </div><!-- panel -->
                                <!-- panel -->
                                @if( $setting->search_ads == 1 && $setting->search_ads_p == 'r' )
                                    <div class="inner-box" >
                                        <!-- ads box -->
                                        {!! $setting->search_adsense !!}
                                    </div>
                                @endif
                                <div class="panel-default panel-faq">
                                    <!-- panel-heading -->
                                    <div class="panel-heading">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#accordion-two">
                                            <h4 class="panel-title">Filters<span class="pull-right"><i class="fa fa-minus"></i></span></h4>
                                        </a>
                                    </div><!-- panel-heading -->
                                    <div id="accordion-two" class="panel-collapse in collapse">
                                        <!-- panel-body -->
                                        <div class="panel-body">
                                            <div id="cf_field">
                                                {{--@foreach($search_fields as $item)
                                                    <div class="">
                                                        <label for="">{{ucfirst($item->name)}}:</label>
                                                        <select name="custom_search[{{ strtolower( str_replace(' ', '_', $item->name) ) }}]" form="search_form" onchange="$('#loading').show();$('#search_form').submit()" class="form-control">
                                                            <option value="">All {{ucfirst($item->name)}} </option>
                                                            @foreach(explode(',', $item->options) as $value)
                                                                <option @if(isset($_REQUEST['custom_search'][strtolower( str_replace(' ', '_', $item->name) )]) && $value == $_REQUEST['custom_search'][strtolower( str_replace(' ', '_', $item->name) )]) selected @endif       value="{{$value}}">{{ ucwords($value) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endforeach--}}

                                                @foreach($search_fields as $item)
                                                    <?php
                                                    $read_more=false;
                                                    ?>
                                                    <div>
                                                    <p for="">{{ucfirst($item->name)}}:</p>
                                                        @php $index=1;  @endphp
                                                            @foreach(explode(',', $item->options) as $value)
                                                                <?php $checked=''; ?>
                                                            @if(isset($_REQUEST['custom_search'][strtolower( str_replace(' ', '_', $item->name) )]) && in_array($value, $_REQUEST['custom_search'][strtolower( str_replace(' ', '_', $item->name) )]) ) <?php $checked='checked'; ?> @endif

                                                            <div class="@if($index > 5 ) hidden rdm @endif" >
                                                                <label class="@if($checked=='checked') checked @endif" for="{{$index}}_{{$item->name}}"> {{ ucwords($value) }} <input form="search_form" name="custom_search[{{ strtolower( str_replace(' ', '_', $item->name) ) }}][]" id="{{$index}}_{{$item->name}}" type="checkbox" {{ $checked }} value="{{$value}}" /></label>
                                                            </div>
                                                            <?php
                                                            if($index == 5 ){
                                                                $read_more = true;
                                                            }
                                                            $index++;
                                                            ?>
                                                        @endforeach
                                                        @if($read_more)
                                                            <a href="javascript:void(0)" class="show_more" onclick="read_more(this)"><small>Show More</small></a>
                                                        @endif
                                                    </div>
                                                        @if(!$loop->last) <hr> @endif
                                                @endforeach
                                            </div>

                                            <hr>
                                            <div class="form-group">
                                                <label for="price_range" class=" control-label"><b>Price ({{$setting->currency}})</b><span class="font-normal text-muted f-s-12 clearfix"></span></label>
                                                <div class="">
                                                    <input type="text" id="price_range" form="search_form" name="price_range">
                                                </div>
                                            </div>
                                            <!--<div class="form-group">
                                                <label for="online" class="">User Online / Offline</label>
                                                <select name="online" id="" class="form-control" form="search_form">
                                                    <option @if( $online == '') selected @endif  value="">All</option>
                                                    <option @if( $online == 1) selected @endif  value="1">Online</option>
                                                    <option @if( $online == 2 ) selected @endif  value="2">Offline</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="img">
                                                    <input type="radio" name="image" value="1" id="img" {{ ($image==1)? 'checked':'' }} form="search_form">
                                                    Image
                                                </label>
                                                <label for="no-img" class="m-l-5">
                                                    <input type="radio" name="image" value="0" id="no-img" {{ ($image==0)? 'checked':'' }} form="search_form">
                                                    Zero image
                                                </label>
                                            </div>-->
                                            <div class="form-group">
                                                <button id="search_btn" class="btn btn-sm btn-success"> <i class="fa fa-search" aria-hidden="true"></i> Search</button>
                                            </div>
                                        </div><!-- panel-body -->
                                    </div>
                                </div><!-- panel -->

                            </div><!-- panel-group -->
                        </div>
                    </div>

                    <!-- recommended-listings -->
                    <div class="col-sm-8 col-md-9">
                        <div class="section recommended-listings">
                            <!-- featured-top -->
                            <div class="featured-top">
                                <h4>Search Result</h4>
                                <div class="dropdown pull-right">

                                @if($setting->hide_price==0)
                                    <!-- category-change -->
                                    <div class="dropdown category-dropdown">
                                        <h5>Sort by:</h5>
                                        <select class="locinput input-rel select-sort-by" data-style="btn-select" data-width="auto" onchange="sortPrice(this.value)">
                                            <option value="">Sort by </option>
                                            <option value="asc">Price: Low to High</option>
                                            <option value="desc">Price: High to Low</option>
                                        </select>
                                    </div><!-- category-change -->
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                            </div><!-- featured-top -->

                            @if(count($result) > 0)
                                @foreach($result as $v)
                                    <?php
                                        if( strlen($v->title) > 35 ){
                                            $ad_title = substr($v->title, 0,35) . '..';
                                        }else{
                                            $ad_title = $v->title;
                                        }
                                        $user_type = \App\User::where('id', $v->user_id)->value('type');

                                    $auction = \App\Auction::where('ad_id', $v->id)->first();
                                    if($auction){
                                    $auction_hour = $auction->hour;
                                    $created = $auction->created_at->addHours($auction_hour);
                                    $now = \Carbon\Carbon::now('UTC');
                                    $minutes = $created->diffInMinutes($now);
                                    $now = \Carbon\Carbon::now('UTC');

                                    if($created > $now){
                                    ?>
                                    <script>
                                        countDown({{$minutes}} ,'.countdown{{$v->id}}');
                                    </script>

                                    <?php
                                        }
                                    }
                                    ?>
                                    <!-- urgent top page price -->
                                    @if($v->f_type == 'top_page_price' || $v->f_type == 'urgent_top_price')
                                        <div class="listings-item row">
                                            <span class="timex countdown{{$v->id}}"></span>
                                            <!-- item-image -->
                                            <div class="item-image-box col-sm-4">
                                                <div class="item-image">
                                                    <a href="{{url('single/'.urlencode( str_slug($v->title.'-'.$v->id) ) )}}">
                                                        @if($v->f_type == 'urgent_top_price')
                                                            <span class="featured-listings urgent-hot">Urgent Hot</span>
                                                        @endif
                                                        @if($v->f_type == 'top_page_price')
                                                            <span class="featured-listings bg-featured">Featured</span>
                                                        @endif
                                                        <img src="{!! asset('assets/images/listings/'.(isset($v->ad_images[0]->image)? $v->ad_images[0]->image : 'empty.jpg').'') !!}" alt="Image" class="img-responsive">
                                                    </a>
                                                </div><!-- item-image -->
                                            </div>
                                            <!-- rending-text -->
                                            <div class="item-info col-sm-8">
                                                <!-- ad-info -->
                                            <!-- ad-info -->
                                                <div class="ad-info">
                                                    @if($setting->hide_price==0)
                                                    <h3 class="item-price pull-right">{{  $setting->currency_place == 'left' ? $setting->currency : ''  }}{{ number_format($v->price) }} {{  $setting->currency_place == 'right' ? $setting->currency : ''  }}</h3>
                                                    @endif
                                                    <h4 class="item-title"><a href="{{url('single/'.urlencode( str_slug($v->title.'-'.$v->id) ) )}}">{{ ucfirst($ad_title) }}</a></h4>
                                                    <div class="item-cat">
                                                        <span class="dated"><a href="javascript:void(0)"> <i class="fa fa-clock-o"></i> {{ $v->created_at->diffForHumans() }}</a></span>
                                                        - <span class="category">{{ $v->category->name }} </span>-
                                                        <span data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ floor($v->visit/2) }} Ads view "><i class="fa fa-eye" aria-hidden="true"> {{ floor($v->visit/2) }}</i></span>
                                                        - <span data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ floor($v->message) }} Message "><i class="fa fa-envelope-o" aria-hidden="true"> {{ floor($v->message) }}</i></span>

                                                    </div>
                                                    @foreach($v->ad_cf_data as $data)
                                                        @if($data->column_value!='')
                                                        <span class="label label-default">@if($data->icon!='' || $data->image !='')  {!! ($data->icon!='')? '<i class="'.$data->icon.'"></i>' : '<img src="'.asset('assets/images/c_icons/'.$data->image.'').'" height="38" width="30" >' !!} @endif {{ ucfirst(str_replace('_', ' ', $data->column_name)) }}: <span class="m-r-5 p-5"> {{$data->column_value}}</span></span>
                                                        @endif
                                                    @endforeach
                                                </div><!-- ad-info -->

                                                <!-- ad-meta -->
                                                <div class="ad-meta">
                                                    <div class="meta-content">
                                                    @if(!Auth::guest())
                                                        <a class="save-add"  data-action="{{ (count($v->save_add) > 0)? 'del': 'ins' }}" data-id="{{$v->id}}" href="javascript:void(0)" title="" >
                                                    @if(count($v->save_add) > 0)
                                                        <span class="fa fa-star"></span>
                                                        <span class="text">Add Saved</span>
                                                    @else
                                                        <span class="fa fa-star-o"></span>
                                                        <span class="text">Save Add</span>
                                                    @endif
                                                    <span class=" hidden fa fa-spinner fa-spin"></span>
                                                    </a>
                                                    @endif
                                                    </div>
                                                <!-- item-info-right -->
                                                    <div class="user-option pull-right">
                                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title=" ad's images"><i class="fa fa-camera"></i> {{ count($v->ad_images) }} </a>
                                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords( $v->city->title ) }}"><i class="fa fa-map-marker"></i> </a>
                                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $user_type == 'u' || $user_type == 'adm'? 'private' : 'Company' }}"><i class="fa {{ $user_type == 'u' || $user_type == 'adm'? 'fa-briefcase' : 'fa-user' }}"></i> </a>

                                                        <a href="javascript:void(0)" class="saved-job">
                                                        @if($v->is_login)
                                                            <i data-toggle="tooltip" title="Online" class="fa fa-circle text-success"></i>
                                                        @else
                                                            <i data-toggle="tooltip" title="Offline" class="fa fa-circle text-danger"></i>
                                                        @endif
                                                        </a>

                                                    </div><!-- item-info-right -->
                                                </div><!-- ad-meta -->
                                            </div><!-- item-info -->
                                        </div>
                                    @endif

                                    @if($v->f_type == 'urgent_price' || $v->f_type == '' || $v->f_type == 'home_page_price')
                                            <div class="listings-item row">
                                                <span class="timex countdown{{$v->id}}"></span>
                                                <div class="item-image-box col-sm-4">
                                                    <!-- item-image -->
                                                    <div class="item-image">
                                                        @if($v->f_type == 'urgent_price')
                                                            <span class="featured-listings urgent">Urgent</span>
                                                        @endif
                                                        @if(isset($v->user->is_verified) && $v->user->is_verified==2)
                                                            <a href="javascript:void(0)" class="verified" data-toggle="tooltip" data-placement="left" title="" data-original-title="Verified"><i class="fa fa-check-square-o"></i></a>
                                                        @endif
                                                            <a href="{{url('single/'.urlencode( str_slug($v->title.'-'.$v->id) ) )}}"><img src="{!! asset('assets/images/listings/'.(isset($v->ad_images[0]->image)? $v->ad_images[0]->image : 'empty.jpg').'') !!}" alt="Image" class="img-responsive"></a>

                                                    </div><!-- item-image -->
                                                </div><!-- item-image-box -->
                                                <!-- rending-text -->
                                                <div class="item-info col-sm-8">
                                                    <!-- ad-info -->
                                                    <!-- ad-info -->
                                                    <div class="ad-info">
                                                        @if($setting->hide_price==0)
                                                            <h3 class="item-price pull-right">{{  $setting->currency_place == 'left' ? $setting->currency : ''  }}{{ number_format($v->price) }} {{  $setting->currency_place == 'right' ? $setting->currency : ''  }}</h3>
                                                        @endif
                                                        <h4 class="item-title"><a href="{{url('single/'.urlencode( str_slug($v->title.'-'.$v->id) ) )}}">{{ ucfirst($ad_title) }}</a></h4>
                                                        <div class="item-cat">
                                                            <span class="dated"><a href="javascript:void(0)"> <i class="fa fa-clock-o"></i> {{ $v->created_at->diffForHumans() }}</a></span>
                                                            - <span class="category">{{ $v->category->name }} </span>-
                                                            <span data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ floor($v->visit/2) }} Ads view "><i class="fa fa-eye" aria-hidden="true"> {{ floor($v->visit/2) }}</i></span>
                                                            - <span data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ floor($v->message) }} Message "><i class="fa fa-envelope-o" aria-hidden="true"> {{ floor($v->message) }}</i></span>

                                                        </div>
                                                        @foreach($v->ad_cf_data as $data)
                                                            @if($data->column_value!='')
                                                                <span class="label label-default">@if($data->icon!='' || $data->image !='')  {!! ($data->icon!='')? '<i class="'.$data->icon.'"></i>' : '<img src="'.asset('assets/images/c_icons/'.$data->image.'').'" height="38" width="30" >' !!} @endif {{ ucfirst(str_replace('_', ' ', $data->column_name)) }}: <span class="m-r-5 p-5"> {{$data->column_value}}</span></span>
                                                            @endif
                                                        @endforeach
                                                    </div><!-- ad-info -->

                                                    <!-- ad-meta -->
                                                    <div class="ad-meta">
                                                        <div class="meta-content">
                                                            @if(!Auth::guest())
                                                                <a class="save-add"  data-action="{{ (count($v->save_add) > 0)? 'del': 'ins' }}" data-id="{{$v->id}}" href="javascript:void(0)" title="" >
                                                                    @if(count($v->save_add) > 0)
                                                                        <span class="fa fa-star"></span>
                                                                        <span class="text">Add Saved</span>
                                                                    @else
                                                                        <span class="fa fa-star-o"></span>
                                                                        <span class="text">Save Add</span>
                                                                    @endif
                                                                    <span class=" hidden fa fa-spinner fa-spin"></span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <!-- item-info-right -->
                                                        <div class="user-option pull-right">
                                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title=" ad's images"><i class="fa fa-camera"></i> {{ count($v->ad_images) }} </a>
                                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords( $v->city->title ) }}"><i class="fa fa-map-marker"></i> </a>
                                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $user_type == 'u' || $user_type == 'adm'? 'private' : 'Company' }}"><i class="fa {{ $user_type == 'u' || $user_type == 'adm'? 'fa-briefcase' : 'fa-user' }}"></i> </a>

                                                            <a href="javascript:void(0)" class="saved-job">
                                                                @if($v->is_login)
                                                                    <i data-toggle="tooltip" title="Online" class="fa fa-circle text-success"></i>
                                                                @else
                                                                    <i data-toggle="tooltip" title="Offline" class="fa fa-circle text-danger"></i>
                                                                @endif
                                                            </a>

                                                        </div><!-- item-info-right -->
                                                    </div><!-- ad-meta -->
                                                </div><!-- item-info -->
                                            </div><!-- ad-listings -->
                                    @endif

                        @endforeach
                            @else
                                <p class="p-20 text-danger"> Ads not found!</p>
                    @endif
                            <!-- pagination  -->
                            <div class="text-center">
                                <ul class="pagination ">
                                    @if(count($result) > 0)
                                        {{ $result->appends(request()->query())->links() }}
                                    @endif
                                </ul>
                            </div><!-- pagination  -->
                        </div>
                    </div>
                </div>
            </div>
    </div>
</section>

    <input class="hidden form-control" type="text" id="search_prm" data-keyword="{{ (isset($_REQUEST['keyword']))? $_REQUEST['keyword'] : ''  }}" data-category="{{ (isset($_REQUEST['category']))? $_REQUEST['category'] : ''  }}" data-city="{{ (isset($_REQUEST['city']))? $_REQUEST['city'] : ''  }}" >
    <script src="{{ asset('assets/plugins/ion-rangeslider/ion.rangeSlider.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/places.js@1.16.6"></script>

    <script>
        $(document).ready(function () {
            (function() {
                var placesAutocomplete = places({
                    appId: 'plDAML4GXDMT',
                    apiKey: '384c249023210b735270be1a32b31358',
                    minLength: 3,
                    container: document.querySelector('#address-input'),
                    templates: {
                        value: function(suggestion) {
                            return suggestion.name;
                        }
                    }
                }).configure({
                    type: 'city',
                    language: 'en'
                });
                placesAutocomplete.on('change', function resultSelected(e) {
                    document.querySelector('#truecity').value = e.suggestion.name || '';
                    document.querySelector('#countryCode').value = e.suggestion.country || '';
                });
            })();
        })
        function read_more(x){
            $(x).parent().find('div.rdm').toggleClass('hidden')
            if($(x).text()=='Show More'){
                $(x).text('Less');
            }else{
                $(x).text('Show More');
            }
        }


        //$(document).ready(function () {
        $('#category  option[value="{{$req_category}}"]').prop("selected", true);
        $('.price_sort_select  option[value="{{$req_price_sort}}"]').prop("selected", true);
        //});

        $(document).ready(function () {
            $("#price_range").ionRangeSlider({
                type: "double",
                grid: true,
                min: 0,
                max: 9000000,
                from: <?= $low_price ?>,
                to: <?= $high_price ?>,
                prefix: "{{$setting->currency}}"
            });
            //search btn

            $('#search_btn').click(function () {
                $('#loading').show();
                $('#search_form').submit();
            });

        });

        function sortPrice(v){

            $('#loading').show();
            $('#price_sort').val(v);
            $('#search_form').submit();
            //console.log('ko');
        }

    </script>
@endsection

