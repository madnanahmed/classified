<?php
$page_url = Request::path();
$setting = DB::table('setting')->first();
// check user is company
$avator = asset('assets/images/users/male.png');
if(Auth::user()->image ==''){
    if(Auth::user()->type == 'c' ){
        $avator = asset('assets/images/users/company.png');
    }
    if(Auth::user()->type == 'u' ){
        if(Auth::user()->gender == 'm'){
            $avator = asset('assets/images/users/male.png');
        }
        if(Auth::user()->gender == 'f'){
            $avator = asset('assets/images/users/female.png');
        }
    }
}
?>
<div class="ad-profile section">
    <div class="user-profile">
        <div class="user-images">
            <img src="{{ $avator }}" height="85px" alt="User Images" >
        </div>
        <div class="user">
            <h2>Hello, <a href="#">{{ ucwords(Auth::user()->name) }}</a></h2>
            <h5>Active user on website since {{ Auth::user()->created_at->diffForHumans() }}</h5>
        </div>
        <div class="favorites-user">
            <div class="my-ads">
                <a href="{{ url('my-ads') }}">{{ \App\Ads::where(['user_id' => Auth::user()->id])->count() }}<small>My ADS</small></a>
            </div>
            <div class="favorites">
                <a href="{{ url('save-ads') }}">{{ \App\SaveAdd::where(['user_id' => Auth::user()->id])->count() }}<small>Favorites</small></a>
            </div>
        </div>
    </div><!-- user-profile -->

    <ul class="user-menu">
        <li class="{{ ($page_url == 'user-panel')? 'active' : '' }}"><a href="{{ url('user-panel') }}"><i class="fa fa-dashboard"></i> Dashboard </a></li>
        <li class="{{ ($page_url == 'my-ads')? 'active' : '' }}"><a href="{{ url('my-ads') }}"><i class="fa fa-list-alt"></i> All <span class="badge">{{ \App\Ads::where(['user_id' => Auth::user()->id])->count() }}</span> </a></li>
        <li class="{{ ($page_url == 'active-ads')? 'active' : '' }}"><a href="{{ url('active-ads') }}"><i class="fa fa-check"></i> Active <span class="badge total_active">{{ \App\Ads::where(['user_id' => Auth::user()->id, 'status' => 1])->count() }}</span></a></li>
        <li class="{{ ($page_url == 'inactive-ads')? 'active' : '' }}"><a href="{{ url('inactive-ads') }}"><i class="fa fa-ban"></i> Inactive <span class="badge total_inactive">{{ \App\Ads::where(['user_id' => Auth::user()->id, 'status' => 2])->count() }}</span></a></li>
        <li class="{{ ($page_url == 'pending-ads')? 'active' : '' }}"><a href="{{ url('pending-ads') }}"><i class="fa fa-clock-o"></i> Pending <span class="badge">{{ \App\Ads::where(['user_id' => Auth::user()->id, 'status' => 0])->count() }}</span></a></li>
        <li class="{{ ($page_url == 'save-ads')? 'active' : '' }}"><a href="{{ url('save-ads') }}"><i class="fa fa-heart"></i> Saved <span class="badge">{{ \App\SaveAdd::where(['user_id' => Auth::user()->id])->count() }}</span></a></li>
        <li class="{{ ($page_url == 'auctioned-ads')? 'active' : '' }}"><a href="{{ url('auctioned-ads') }}"><i class="fa fa-gavel"></i> Auctioned <span class="badge">{{ \App\Auction::where(['user_id' => Auth::user()->id])->count() }}</span></a></li>
        <li class="{{ ($page_url == 'my-bids')? 'active' : '' }}"><a href="{{ url('my-bids') }}"><i class="fa fa-check"></i> My Bids <span class="badge">{{ \App\UserBids::where(['user_id' => Auth::user()->id ])->count() }}</span></a></li>
        <li class="{{ ($page_url == 'message')? 'active' : '' }}"><a href="{{ route('message.index') }}"><i class="fa fa-envelope-o"></i> Message <span class="badge">{{ \App\Message::where(['to' => Auth::user()->id])->count() }}</span></a></li>
    </ul>
</div>
