<?php

Route::get('tst', 'AjaxController@test');

/*cron*/
Route::get('auction_cron', 'AdsController@auction_cron')->name('auction_cron');
/*stripe*/
Route::get('addmoney/stripe', array('as' => 'addmoney.paywithstripe','uses' => 'AddMoneyController@payWithStripe'));
Route::post('addmoney/stripe', array('as' => 'addmoney.stripe','uses' => 'AddMoneyController@postPaymentWithStripe'));

Route::get('verify_ad', 'HomeController@verifyAd')->name('verify_ad');

Route::get('/update-version', function() {
    return view('admin.update.index');
});

Route::get('login/facebook', 'Auth\LoginController@redirectToFBProvider');
Route::get('login/google', 'Auth\LoginController@redirectToGoogleProvider');
Route::get('facebook/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('login/google/callback', 'Auth\LoginController@handleGoogleProviderCallback');

Route::get('showrooms', 'AdsController@showrooms')->name('showrooms');
Route::get('showrooms/ads/{id}', 'AdsController@showrooms_ads');




Route::get('/clear', function() {

    /*Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');*/
   // Artisan::call('config:cache');
   // return redirect('/')->with('success', 'cache clear successfully');
});

Route::get('/', 'HomeController@index');
Route::post('save-contact-form', 'HomeController@saveContactForm')->name('save-contact-form');
Route::get('location', 'HomeController@mapListings')->name('location');

/* contact us */
Route::get('contact-us', function (){
    return view('contact_us');
})->name('contact-us');

Route::get('page/{title}', 'HomeController@showCustomPages');

/*  mobile verify */
Route::resource('mobile_verify', 'MobileVerifyController');
Route::post('phone-verify-code', 'MobileVerifyController@phoneVerifyCode')->name('phone-verify-code');
Route::post('verify-code', 'MobileVerifyController@verifyCode')->name('verify-code');

Route::get('get-bid-history', 'AdsController@get_bid_history')->name('get-bid-history');
Route::get('active-auction', 'AdsController@auction')->name('active-auction');
Route::post('auction_expired', 'AdsController@auction_expired')->name('auction_expired');
Route::post('get_bids_data', 'AdsController@get_bids_data')->name('get_bids_data');

Auth::routes();
Route::resource('admin-login', 'LoginController');
Route::resource('category', 'CategoryController');
Route::resource('ads', 'AdsController');
Route::resource('region', 'RegionController');
Route::resource('chat', 'ChatController');
//Route::resource('search', 'SearchController');
// update test route
Route::get('updates', 'HomeController@update');

//update
Route::get('update-version', 'HomeController@updateVersion');
// up server
Route::get('update_server/query', 'HomeController@updateServer');

Route::post('load-category', 'AdsController@loadCategory');
Route::post('upload-ads-images', 'AdsController@uploadImages');
Route::post('delete-ads-images', 'AdsController@deleteImages');
Route::get('user-ads/{id}', 'AdsController@userAds');
Route::get('ad-message/{id}', 'AdsController@adSuccess');

// crone
Route::get('ads_cron', 'CronController@index');
Route::get('is_login', 'HomeController@is_login');

//get requests
Route::get('/home', 'HomeController@index')->name('home');
//Route::get('admin/login', 'LoginController@index');
Route::get('sale/{any}', 'CategoryController@saleCategory');
Route::get('single/{any}', 'AdsController@singleAd')->name('single');
Route::post('check_email', 'AdsController@checkEmail');
Route::post('user-login', 'AdsController@userLogin');

//Route::get('search{category}/{region}/{keyword}', 'SearchController@search');
Route::get('search/query', 'SearchController@search');
//confirm email of user
Route::get('confirm/query', 'HomeController@userConfirm');
Route::post('ajax-search', 'SearchController@ajaxSearch');
// category
Route::post('load-city', 'RegionController@loadCity');
Route::post('load-comune', 'RegionController@loadComune');
Route::post('load-customfields', 'AjaxController@loadCustomFields');
Route::get('load-price_option', 'AjaxController@loadPriceOption');
Route::post('load-edited-customfields', 'AjaxController@loadEditedCustomFields');
Route::post('load-child-cat', 'AjaxController@loadChildCat')->name('load-child-cat');

Route::post('user-rating', 'AjaxController@userRating')->name('user-rating');
## User Only
Route::group(['middleware' => ['auth']], function () {
    Route::resource('user-panel', 'UserPanelController');
    Route::resource('setting', 'SettingController');


    Route::get('auctioned-ads', function (){
        return view('user.auctioned_ads');
    });

    Route::post('store-auction', 'AdsController@store_auction')->name('store-auction');
    Route::post('check-auction', 'AdsController@check_auction')->name('check-auction');
    Route::post('accept-bid', 'AdsController@accept_bid')->name('accept-bid');
    Route::post('reject-bid', 'AdsController@reject_bid')->name('reject-bid');

    Route::post('save-bid', 'AdsController@save_bid')->name('save-bid');

    Route::get('bidhistory', 'AdsController@bidhistory')->name('bidhistory');


    Route::get('my-bids', function (){
        return view('user.my_bids');
    });

    Route::get('load-showrooms', 'AjaxController@loadShowrooms')->name('load-showrooms');



    Route::post('user-profile-settings', 'UserPanelController@UserprofileSetting');
    Route::post('change-password', 'UserPanelController@changePassword');
    Route::get('my-ads', 'UserPanelController@myAds');
    Route::get('pending-ads', 'UserPanelController@pendingAds');
    Route::get('active-ads', 'UserPanelController@activeAds');
    Route::get('inactive-ads', 'UserPanelController@inactiveAds');
    Route::post('user-id-card', 'UserPanelController@userIdCard');
    Route::post('contact-user', 'UserPanelController@contactUser');
    Route::get('save-ads', 'UserPanelController@saveAds');
    // update login status
    Route::get('login_status', 'UserPanelController@login_status');
    //chat
    Route::resource('chat', 'ChatController');
    Route::post('load_chat_head', 'ChatController@loadChatHead')->name('load_chat_head');
    Route::post('load_chat_message', 'ChatController@loadChatMessage')->name('load_chat_message');
    Route::get('load-emoji', 'ChatController@loadEmoji');
    Route::get('load-block-users', 'ChatController@load_block_users');
    Route::get('lock_all', 'ChatController@lock_all');

    //Message
    Route::resource('message', 'MessageController');
    Route::post('load_message_head', 'MessageController@loadMessageHead')->name('load_message_head');
    Route::post('load_message', 'MessageController@loadMessage')->name('load_message');
    Route::post('message_notify', 'MessageController@messageNotify')->name('message_notify');
    Route::get('load-emoji', 'MessageController@loadEmoji');
    Route::get('load_message_reference', 'MessageController@load_message_reference');
    // SETTING
    Route::post('adsense-store', 'SettingController@adsenseStore');
    Route::post('theme-css', 'SettingController@themeCss')->name('theme-css');
    // ajax
    Route::get('load-my-ads', 'AjaxController@loadMyAds');
    Route::get('load-users', 'AjaxController@loadUsers')->name('load-users');
    Route::get('load-save-ads', 'AjaxController@loadSaveAds')->name('load-save-ads');

    Route::get('load-auction-ads', 'AjaxController@loadAuctionAds')->name('load-auction-ads');

    Route::get('get-my-bids', 'AjaxController@get_my_bids')->name('get-my-bids');

    Route::post('delete-category', 'AjaxController@deleteCategory');
    Route::post('delete', 'AjaxController@delete')->name('delete');
    Route::post('change-status', 'AjaxController@changeStatus');
    Route::get('load-ads-list', 'AjaxController@loadAdsLists');

    // save add
    Route::get('save-add', 'AjaxController@saveAdd');
    //regions
    Route::get('load-region', 'AjaxController@loadRegions')->name('load-region');
    Route::get('load-city', 'AjaxController@loadCity')->name('load-city');
});

## Admin Only
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'checkAdmin']], function () {
    Route::resource('admin', 'AdminController');
    Route::resource('users', 'UserController');
    Route::resource('region', 'RegionController');
    Route::resource('email-settings', 'UserEmailSettings');
    Route::resource('customfields', 'CustomfieldsController');
    Route::resource('groups', 'GroupsController');
    Route::resource('group_fields', 'GroupFieldsController');
    Route::resource('cf_enhance', 'CfEnhanceController');
    Route::resource('custom-page', 'CustomPageController');
    Route::resource('featured-ads', 'FeaturedAdsController');

    Route::post('save-payment-gateway', 'FeaturedAdsController@paymentGatewaySave')->name('save-payment-gateway');

    Route::get('dashboard', 'AdminController@index');
    Route::get('profile', 'UserController@index');
    Route::post('profile-settings', 'UserController@profileSetting');
    Route::get('users', 'UserController@loadUsers');
    Route::post('user/loadEdit', 'UserController@loadEdit');
    Route::get('show-card', 'UserController@showCard');
    Route::get('vfy-card', 'UserController@vfyCard');
    // Custom Fields routes
    Route::get('customfields', 'CustomfieldsController@index');
    Route::post('customfields/edit', 'CustomfieldsController@store');
    Route::post('customfields-remove', 'CustomfieldsController@removeCustomField');
    Route::get('customfields/newcfield', 'CustomfieldsController@newCField');
    // locations
    Route::get('region', 'AdminController@regionCreate');
    Route::get('edit-region/{id}', 'AdminController@editRegion');
    Route::post('region', 'AdminController@storeRegion');
    // city
    Route::get('city', 'AdminController@cityCreate');
    Route::get('edit-city/{id}', 'AdminController@editCity');
    Route::post('city', 'AdminController@storeCity');

    Route::post('email-settings-store', 'UserEmailSettings@saveEmailSettings')->name('email-settings-store');
    Route::post('test-email', 'UserEmailSettings@testEmail')->name('test-email');

    Route::get('sort-pages', 'CustomPageController@sortPages')->name('sort-pages');

    Route::get('showrooms-list', function (){
        return view('admin.ads.company');
    });
});
