<?php

namespace App\Http\Controllers;
use App\AdsImages;
use App\Auction;
use App\City;
use App\FeaturedAds;
use App\PaymentGateway;
use App\Setting;
use App\UserBids;
use Carbon\Carbon;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use App\Category;
use App\Ads;
use App\User;
use App\CustomFieldData;
use App\GroupData;
use Image;
use Session;
use DB;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use Mail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\URL;

class AdsController extends Controller
{
    use AuthenticatesUsers;

    protected $file_path = '/assets/images/listings/';
    private  $category , $region, $notification_subject, $notification_to_email = '';
    private $custom_search = false;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        error_reporting(0);
    }

    public function index()
    {
        if(!Auth::guest()) {
            if(Auth::user()->type =='adm'){
                return view('admin.ads.list');
            }else{
                return back();
            }
        }else{
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $region = DB::table('region')->get();
        $parent_cat = Category::select('name','slug','icon', 'image')->where('parent_id',0)->get();

        $featured = FeaturedAds::first();

        return view('ads.create', compact('region', 'parent_cat', 'featured'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $is_guest=$is_edit=$isAdminAd=$featured_success=$is_paypal = false;
        try {
            /* featured add payment */
            if($request->ad_price!=0 && $request->ad_price == $request->featured_amount)
            {
                $validator = Validator::make($request->all(), [
                    'card_no' => 'required',
                    'ccExpiryMonth' => 'required',
                    'ccExpiryYear' => 'required',
                    'cvvNumber' => 'required',
                    //'amount' => 'required',
                ]);

                if ($validator->passes()) {
                    $api_key = PaymentGateway::select('*')->first();
                    $stripe = Stripe::make($api_key->stripe_secret_key);
                    $token = $stripe->tokens()->create([
                        'card' => [
                            'number' => $request->get('card_no'),
                            'exp_month' => $request->get('ccExpiryMonth'),
                            'exp_year' => $request->get('ccExpiryYear'),
                            'cvc' => $request->get('cvvNumber'),
                        ],
                    ]);

                    if (!isset($token['id'])) {
                        return response()->json(['msg' => 'error']);
                        exit;
                    }
                    $charge = $stripe->charges()->create([
                        'card' => $token['id'],
                        'currency' => 'USD',
                        'amount' => $request->ad_price,
                        'description' => 'Add in wallet',
                    ]);

                    if($charge['status'] = 'succeeded') {
                        $featured_success = true;
                    } else {
                        return response()->json(['msg' => 'error_f']);
                        exit;
                    }
                }else{
                    return response()->json(['msg' => 'fill_required']);
                    exit;
                }
            }

            $obj = new Ads();
            if ($request->id != '')
            {
                $obj = $obj->findOrFail($request->id);
                $obj->title = str_replace('-',"", $request->title);
                $is_edit = true;
            }else{
              if(isset($request->paypal) && $request->paypal=='yes'){
                $is_paypal = true;
                  $obj->status = 3;
              }else{
                  $obj->status = 0;
              }
            }

            $obj->fill($request->all());

            /*add location data*/

           $city = City::updateOrCreate(['title' => $request->city, 'country_code' => $request->country_code]);

           $obj->city_id = $city->id;

            if (!Auth::guest())
            {
                if (Auth::user()->type == 'adm')
                {
                    if ($is_edit)
                    {
                        $user_id = $request->user_id;
                    }else{
                        $user_id = Auth::user()->id;
                    }

                    $obj->status = 1;
                    if ($user_id == Auth::user()->id)
                    {
                        $isAdminAd = true;
                    }
                    if(Auth::user()->phone==''){
                        if($request->phone!=''){
                            User::where('id', auth::user()->id)->update(['phone' => $request->phone]);
                        }
                    }
                }else{
                    $user_id = Auth::user()->id;
                    $obj->user_id = $user_id;
                }
                User::where( 'id', Auth::user()->id )->update( ['phone' => $request->phone] );
            }else{
                $is_guest = true;

                $temp_password = 'temp_'.rand(1,999);

                $user_data = [
                    'phone' => $request->phone,
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'password' => bcrypt($temp_password),
                    'plain_password' => $temp_password,
                    'type' => 'u',
                    'created_at' => DB::raw('NOW()')
                ];

                if(session()->has('mobile_vfy')){
                    $user_data = $user_data + array('mobile_verify' => 1 );
                }
                $user_id = User::insertGetId($user_data);
                User::where('id', $user_id)->update( ['phone' => $request->phone] );
                $email_temp = DB::table('email_settings')->select('registration_subject','registration_content')->first();
                //print_r($email_temp);
                $subject = $email_temp->registration_subject;
                $content = $email_temp->registration_content;

                $content = str_replace('%email%', $request->user_email, $content);
                $content = str_replace('%name%', $request->user_name, $content);
                $content = str_replace('%password%', $temp_password, $content);
                $content = str_replace('%status%', 'Guest User', $content);

                $this->notification_to_email = $request->user_email;
                $this->notification_subject = $subject;

                $url = base64_encode('u'.'%'.$request->user_email.'%'.$request->user_name);
                $link = url('confirm/query?user='.str_replace('=', '', $url));
                $data = array('link' => $link ,'name' => $request->user_name, 'content' => $content, 'subject' => $subject );

                Mail::send('emails.notification', $data, function($msg){
                    $msg->subject($this->notification_subject);
                    $msg->to($this->notification_to_email);
                });
                if(!$is_paypal){
                  $obj->status = 4; // guest add
                }
            }
            $obj->user_id = $user_id;
            if ($request->image_ids != '')
            {
                $obj->is_img = 1;
            }
            if ($obj->save())
            {
                $ad_id = $obj->id;
                $type='';
                // delete old data of cf
                CustomFieldData::where('ad_id', $request->id)->delete();
                // delete old data of groupd
                GroupData::where('ad_id', $request->id)->delete();
                // images

                if ($request->image_ids != '')
                {
                    $unique = '';
                    $img_raw = explode(',', $request->image_ids);
                    // get images ids
                    if ($request->deleted_ids != '')
                    {
                        $remove_images = explode(',', $request->deleted_ids);
                        $unique = array_diff($img_raw, $remove_images);
                    }
                    if (!is_array($unique))
                    {
                        $unique = explode(',', $request->image_ids);
                    }
                    // db code here
                    DB::table('ads_images')->whereIn('id', $unique)->update(['ad_id' => $ad_id]);
                }
                // cf data
                if (is_array($request->cf))
                {
                    //print_r($request->cf);
                    foreach ($request->cf as $index => $value)
                    {
                        $key =  key($value);
                        // check if data is file
                        if (is_object($value[$key]))
                        {
                            $file_name = $input[$index] = time() . '.' . $value[$key]->getClientOriginalExtension();
                            $value[$key]->move(base_path() . '/assets/images/cf_image/', $input[$index]);
                            $type = $value[$key]->getClientOriginalExtension();
                            $value[$key] = $file_name;
                        }
                        $id = DB::table('custom_field_data')
                            ->insertGetId(
                                [
                                    'ad_id' => $ad_id,
                                    'column_name' => $index,
                                    'column_value' => $value[$key],
                                    'type' => $type,
                                    'cf_id' => $key
                                ]
                            );
                    }
                }
                // Group data
                if (is_array($request->group))
                {
                    //print_r($request->cf);
                    foreach ($request->group as $i => $val)
                    {
                        $key =  key($val);
                        $temp = explode('#', $i);
                        $id = DB::table('group_data')
                            ->insertGetId(
                                [
                                    'ad_id' => $ad_id,
                                    'column_name' => $temp[0],
                                    'column_value' => $val[$key],
                                    'group_id' => $key,
                                    'group_field_id' => $temp[1]
                                ]
                            );
                    }
                }
            }
        }catch(\Exception $e){
            $err = $e->getMessage();
        }

        if (isset($err))
        {
            return response()->json(['msg' => 2, 'error' => $err]);
        }else{
            if ($isAdminAd)
            {
                return response()->json(['msg' => 'admin_ad']);

            }else {
                if ($is_guest)
                {
                    if(isset($request->paypal) && $request->paypal=='yes'){
                        $payment_id = DB::table('payment_data')->insertGetId(['ad_data' =>  time().'%'.$ad_id ]);
                        return response()->json(['msg' => 4, 'ad_id' => $payment_id]);
                    }else{
                        return response()->json(['msg' => 4]);
                    }
                } else {
                    if(isset($request->paypal) && $request->paypal=='yes'){
                        $payment_id = DB::table('payment_data')->insertGetId(['ad_data' =>  time().'%'.$ad_id ]);
                        return response()->json(['msg' => 1, 'ad_id' => $payment_id]);
                    }else{
                        return response()->json(['msg' => 1]);
                    }
                }
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::guest())
        {
            return back()->with('error', 'Unauthorised!');
            exit;
        }

        $region = DB::table('region')->get();
        $parent_cat = Category::select('name','slug','icon', 'image')->where('parent_id',0)->get();
        // ad data
        $id = Ads::whereId($id )->value('id');
        //$ad_id = User::where();
        if ($id)
        {
            $ad_data = Ads::with('ad_images', 'category', 'city')
                ->where('id', $id)
                ->first()->toArray();
            //groups data
            $groups = array();
            $grup = DB::table('group_data')
                ->join('groups', 'group_data.group_id', 'groups.id')
                ->select(
                    'groups.id as group_id',
                    'groups.title',
                    'groups.icon',
                    'groups.image'
                )
                ->where('group_data.ad_id', $id)
                ->groupBy('group_data.group_id')
                ->get()->toArray();

            foreach ($grup as $v)
            {
                $group_fields = DB::table('group_fields')
                    ->join('group_data', 'group_data.group_id', 'group_fields.group_id')
                    ->select(
                        'group_fields.title',
                        'group_fields.icon',
                        'group_fields.image',
                        'group_fields.id',
                        'group_data.column_name',
                        'group_data.column_value'
                    )
                    ->where(['group_data.ad_id' => $id, 'group_data.group_id' => $v->group_id, 'group_fields.status' => 1])
                    ->groupBy('group_fields.id')
                    ->get()
                    ->toArray();
                array_push($groups, [
                    'group_title' => $v->title,
                    'group_id' => $v->group_id,
                    'group_icon' => $v->icon,
                    'group_image' => $v->image,
                    'group_fields' => $group_fields,
                ]);
            }
        }
        return view('ads.create', compact('parent_cat', 'region', 'ad_data', 'groups'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function loadCategory(Request $request)
    {
        $id = $request->id;
        $categories = Category::parent($id)->renderAsArray();

        return view('ads.load_categories', compact('categories'));
    }

    public function uploadImages(Request $request)
    {
        if(is_array($_FILES)) {
            $file = $request->file('image');
            $file = $file[0];
            $file_info 	= uniqid().'_'.time().'.'. $file->getClientOriginalExtension() ;
            $folderPath = base_path() . $this->file_path;
            Image::make( $_FILES['image']['tmp_name'][0] )->resize(null, 409, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($folderPath . $file_info )
            ->destroy();

            echo $insert_id = DB::table('ads_images')->insertGetId(['orignal_filename' => $_FILES['image']['name'][0], 'image' => $file_info]);
        }
    }

    private function imageResize($imageResourceId,$width,$height) {
        $targetWidth =672;
        $targetHeight =503;
        $targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
        imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
        return $targetLayer;
    }

    /*
     * delete image
    */
    public function deleteImages(Request $request)
    {

        $file = DB::table('ads_images')->select('image', 'id')->where('orignal_filename', '=', $request->file)->first();
        if ($file->image)
        {
            if (file_exists(base_path('assets/images/listings/' . $file->image)))
            {
                unlink(base_path('assets/images/listings/' . $file->image));
                $del = DB::table('ads_images')->where('image', $file->image)->delete();
                if ($del)
                {
                    echo $file->id;
                }
            }
        }
    }// end

    function singleAd(Request $request)
    {
        try {
            $slug = \Request::segment(2);
            $raw_id = explode('-', $slug);
            $raw_id = end($raw_id);
            $user = Ads::where(['id' => $raw_id])->first();
            $id = $user->id;
            $user_id = $user->user_id;
            $user_ip =  $request->ip();

            if ($id)
            {
                //increment of a visits
                Ads::where(['id' => $id])->increment('visit');
                $data = Ads::with('ad_images', 'user', 'category', 'city')
                    ->where('id', $id)
                    ->first();

                $ad_cf_data = DB::table('custom_field_data')
                    ->join('customfields', 'customfields.id', 'custom_field_data.cf_id')
                    ->select('customfields.type as type', 'customfields.inscription', 'customfields.type','customfields.icon','customfields.image', 'custom_field_data.column_name', 'custom_field_data.column_value', 'custom_field_data.type as img')
                    ->where(['custom_field_data.ad_id' => $id])
                    ->get()
                    ->toArray();

                $groups = array();
                $grup = DB::table('group_data')
                    ->join('groups', 'group_data.group_id', 'groups.id')
                    ->select(
                        'groups.id as group_id',
                        'groups.title',
                        'groups.icon',
                        'groups.image'
                    )
                    ->where('group_data.ad_id',$id)
                    ->groupBy('group_data.group_id')
                    ->get()->toArray();

                foreach($grup as $v)
                {
                    $group_fields = DB::table('group_fields')
                        ->join('group_data', 'group_data.group_field_id', 'group_fields.id')
                        ->select(
                            'group_fields.title',
                            'group_fields.icon',
                            'group_fields.image',
                            'group_fields.id',
                            'group_data.column_name',
                            'group_data.column_value'
                        )
                        ->where([ 'group_data.ad_id' => $id , 'group_data.group_id' => $v->group_id, 'group_fields.status' => 1])
                        ->get()
                        ->toArray();
                    array_push( $groups,  [
                        'group_title' => $v->title,
                        'group_id' => $v->group_id,
                        'group_icon' => $v->icon,
                        'group_image' => $v->image,
                        'group_fields' => $group_fields,
                    ]);
                }
                /// ad to stats

                DB::table('profile_visit')->insert(['ads_view' => 1, 'ip' => $user_ip, 'user_id' => $user->user_id ]);
                return view('ads.single', compact('data', 'groups', 'ad_cf_data', 'user_ip'));
            }else{
                return  back()->with('error', 'Add not found!');
            }

        }catch(\Exception $e)
        {
            $err =  $e->getMessage();
        }
        if (isset($err))
        {
            return  back()->with('error', 'Add not found!');
        }

    }
    public function userAds(Request $request)
    {
        if (\Request::segment(2) !="")
        {
            $user_id = \Request::segment(2);
            $check = User::whereId($user_id)->value('id');
            if (!$check)
            {
                return redirect()->back(); exit;
            }
            $result = array();
            $sql_search = Ads::with(array('ad_cf_data' => function ($query)
                {
                    $query->join('customfields', 'customfields.id', '=', 'custom_field_data.cf_id');
                    $query->where('is_shown', 1);
                },
                    'ad_images', 'city', 'category', 'region','user'
                )
            )->where('user_id', $user_id);

            $sql_search = $sql_search->where('status', 1);
            $total = $sql_search->count();
            if ($total > 0) {
                $sql_search = $sql_search
                    ->paginate(10)
                    ->appends(request()
                        ->query());


                $result = $sql_search;
            }

            $ip = $_SERVER['REMOTE_ADDR'];
            DB::table('profile_visit')->insert(['profile_view' => 1, 'ip' => $ip, 'user_id' => $user_id]);

            return view('user.user_ads', compact('result', 'total'));
        }
    }

    function checkEmail(Request $request)
    {

        $email = User::where(['email' => $request->email])->value('email');
        if ($email)
        {
            echo $email;
        }else{
            echo 2;
        }
    }

    function userLogin(Request $request)
    {

        $auth = false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember')))
        {
            $auth = true; // Success
        }

        if ($auth == true)
        {
            return response()->json([
                'auth' => $auth,
                'intended' => URL::previous(),
                'msg'=>1,
                'name' =>  Auth::user()->name,
                'phone' => Auth::user()->phone
            ]);

        } else {
            return response()->json(['msg' => 2]);
            //redirect again to login view with some errors line 3
        }
    }
    function adSuccess($id)
    {
        return view( 'user.ad_success', compact('id')  );
    }

    function check_auction(Request $request){

       $auction = Auction::where('ad_id', $request->id)
           ->where('user_id', auth()->user()->id)
           ->first();

        if($auction){
            return response()->json(['success' => true, 'data' => $auction]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    function store_auction(Request $request){

        $obj = new Auction();
        if ($request->auction_id != '')
        {
            $obj = $obj->findOrFail($request->auction_id);
        }

        $obj->fill($request->all());

        $obj->ad_id = $request->id;
        $obj->user_id = auth()->user()->id;

        if($request->has('now')){
            $obj->created_at = Carbon::now('UTC');

        }

        if($request->has('status')){
            $obj->status = 1;
        }else{
            $obj->status = 0;
        }

        if($obj->save()){
            /*update price*/
            Ads::where('id', $request->id)->update([ 'price'=> $request->price ]);
            return response()->json(['success' => true, 'message' => 'auction saved successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Some unknown error']);
        }
    }

    function auction(){

        $now = Carbon::now('UTC');

        $auction = Auction::where('status', 1)
            ->whereRaw("DATE_ADD(created_at, interval hour hour) >= '$now' ")
            ->pluck('ad_id');

        $result = array();
        $sql_search = Ads::with(array('ad_cf_data' => function ($query)
            {
                $query->join('customfields', 'customfields.id', '=', 'custom_field_data.cf_id');
                $query->where('is_shown', 1);
            },
                'ad_images', 'city', 'category', 'region','user'
            )
        )->whereIn('id', $auction);

        $sql_search = $sql_search->where('status', 1);
        $total = $sql_search->count();
        if ($total > 0)
        {
            $result = $sql_search->paginate(10);
        }

        return view('auction.view', compact('result'));
    }

    function save_bid(Request $request){

        if($request->auction_id !='' ){
            $auction = Auction::where('id', $request->auction_id)->where('status', 1)->first();
            if($auction){

                $max_bid = UserBids::where('auction_id',$request->auction_id )->max('amount');

                /* own ad bid */
                if( auth()->user()->id == $auction->user_id ){
                    return response()->json(['success' => false, 'max_bid' => $max_bid, 'message' => "You can't bid at your own ad!" ]);
                }
                /* bid less than min */
                if($request->amount < $auction->price){
                    return response()->json(['success' => false, 'max_bid' => $max_bid, 'message' => 'You bid is less than minimum price!' ]);
                }
                /*bid less than max amount*/
                if($request->amount <= $max_bid ){
                    return response()->json(['success' => false, 'max_bid' => $max_bid, 'message' => "You bid is less than top max bid price!" ]);
                }

                if($request->amount < $auction->price){
                 return response()->json(['success' => false, 'max_bid' => $max_bid, 'message' => 'You bid is less than minimum price!' ]);
                }

                UserBids::create(['auction_id' => $request->auction_id, 'user_id' => auth()->user()->id, 'amount' => $request->amount ]);

                $bidHis = UserBids::where('auction_id', $auction->id)->count();
                $max_bid = UserBids::where('auction_id',$request->auction_id )->max('amount');
                return response()->json(['success' => true, 'message' => 'Your bid placed successfully!',  'max_bid' => $max_bid, 'history' => $bidHis ]);
            }else{
                return response()->json(['success' => false,'max_bid' => 0]);
            }
        }

    }

    function get_bid_history(Request $request){

        $auction_id = Auction::where('ad_id', $request->id)->value('id');

        if($auction_id) {
            $bids = UserBids::where('auction_id', $auction_id);

            $count = 0;
            return Datatables::of($bids)
                ->editColumn('id', function ($data) {
                    global $count;
                    $count++;
                    return $count;
                })
                ->addColumn('name', function ($data) {
                   $name = User::where('id', $data->user_id)->first();
                   if($name){
                       return  $name->name;
                   }else{
                       return 'unknown';
                   }
                })
                ->addColumn('time', function ($data) {
                   $time = $data->created_at->diffForHumans();
                    return $time;
                })
                ->rawColumns(['title', 'action'])
                ->make(true);
        }
    }

    function bidhistory(Request $request){

        $auction_id = Auction::where('ad_id', $request->id)->value('id');

        if($auction_id) {
            $bids = UserBids::where('auction_id', $auction_id);

            $count = 0;
            return Datatables::of($bids)
                ->editColumn('id', function ($data) {
                    global $count;
                    $count++;
                    return $count;
                })
                ->addColumn('name', function ($data) {
                    $name = User::where('id', $data->user_id)->first();
                    if($name){
                        return  $name->name;
                    }else{
                        return 'unknown';
                    }
                })
                ->addColumn('time', function ($data) {
                    $time = $data->created_at->diffForHumans();
                    return $time;
                })
                ->addColumn('action', function ($data) use ($auction_id) {
                    $b='';
                    if(!UserBids::where('auction_id', $auction_id)->where('won', 1)->exists() ){
                        $b .= '<button title="Accept Bid" class="btn btn-xs btn-success" onclick="accept_bid('.$data->id.')"><i class="fa fa-check"></i></button>';
                    }else{
                        if($data->won ==1){
                            $b .= '<button title="Bid Accepted" class="btn btn-xs btn-success">accepted</button>';
                            $b .= '&nbsp;<button title="Reject this Bid" onclick="reject_bid('.$data->id.')" class="btn btn-xs btn-danger">Reject</button>';
                        }
                    }
                    return $b;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    function accept_bid(Request $request){

        $bid = UserBids::find($request->id);
        if($bid) {
            $auction = Auction::find($bid->auction_id);
            $auction->update(['status' => 0]);
            $up = $bid->update(['won' => 1]);

            if ($up) {
                $user = User::find($bid->user_id);
                if($user) {
                    $currency = Setting::value('currency');
                    $to = $bid->user_id;
                    $ad_id = $auction->ad_id;
                    $ads = Ads::where('id', $ad_id)->first();
                    if($ads) {
                    $ad_url = url('single/'.urlencode( str_slug($ads->title.'-'.$ads->id) ) );
                    $text = "<strong>Hi ".ucfirst($user->name)."  Your bid accepted</strong><br>";
                    $text.='<strong>Bid price:</strong> '.$currency.' '.$bid->amount;
                    $text.='<a target="_blank" href="'.$ad_url.'"><h3>Ad Title: '.$ads->title.'</h3></a>';

                    $ads_image = AdsImages::where('ad_id', $ad_id)->first();
                    if($ads_image){
                        $path = 'assets/images/listings/'.$ads_image->image;
                        $text .="<img src='".asset($path)."' alt='img'><br>";
                    }

                    $text .= $ads->description;

                    $identifier = Auth::user()->id . ':' . $to;
                    $from = Auth::user()->id;
                    DB::table('message')->insertGetId(['identifier' => $identifier, 'text' => $text, 'from' => $from, 'to' => $to, 'ad_id' => $ad_id]);

                        try {
                            $data = array('content' => $text);
                            Mail::send('emails.mail', $data, function ($msg) use ($user) {
                                $msg->subject('Your bid accepted');
                                $msg->to($user->email);
                            });
                        } catch (\Exception $e) {

                        }
                    }
                }
                return response()->json(['success' => true, 'message' => 'Bid accepted successfully!']);
            } else {
                return response()->json(['success' => false]);
            }
        }else{
            return response()->json(['success' => false]);
        }
    }

    function reject_bid(Request $request){

        $bid = UserBids::find($request->id);
        if($bid) {
            $up = $bid->update(['won' => 2]);
            $user = User::find($bid->user_id);
            if ($up) {
                if($user) {
                    $auction = Auction::find($bid->auction_id);
                    $currency = Setting::value('currency');
                    $to = $bid->user_id;
                    $ad_id = $auction->ad_id;
                    $ads = Ads::where('id', $ad_id)->first();
                    if($ads) {
                        $ad_url = url('single/'.urlencode( str_slug($ads->title.'-'.$ads->id) ) );
                        $text = "<strong>Hi ".ucfirst($user->name)."!  Your bid has been rejected</strong><br>";
                        $text.='<strong>Bid price:</strong> '.$currency.' '.$bid->amount;
                        $text.='<a target="_blank" href="'.$ad_url.'"><h3>Ad Title: '.$ads->title.'</h3></a>';

                        $ads_image = AdsImages::where('ad_id', $ad_id)->first();
                        if($ads_image){
                            $path = 'assets/images/listings/'.$ads_image->image;
                            $text .="<img src='".asset($path)."' alt='img'><br>";
                        }

                        $text .= $ads->description;

                        $identifier = Auth::user()->id . ':' . $to;
                        $from = Auth::user()->id;
                        DB::table('message')->insertGetId(['identifier' => $identifier, 'text' => $text, 'from' => $from, 'to' => $to, 'ad_id' => $ad_id]);

                        try {
                            $data = array('content' => $text);
                            Mail::send('emails.mail', $data, function ($msg) use ($user) {
                                $msg->subject('Your bid has been rejected');
                                $msg->to($user->email);
                            });
                        } catch (\Exception $e) {

                        }
                    }
                }
                return response()->json(['success' => true, 'message' => 'Bid rejected successfully!']);
            }else{
                return response()->json(['success' => false]);
            }
        }
    }

    function auction_expired(Request $request){

        $auction =Auction::find($request->id);
        if($auction){
            //$auction->update(['status' => 0]);
        }

        if(!Auth::guest()){
            $max_bid = UserBids::where('auction_id',$request->id )->max('amount');
            return response()->json(['success' => true, 'max_bid' => $max_bid, 'user_id' => auth()->user()->id]);
        }
    }

    function get_bids_data(Request $request){
        if($request->id !='' ){
            $auction = Auction::where('id', $request->id)->where('status', 1)->first();
            if($auction){
                $max_bid = UserBids::where('auction_id',$request->id )->max('amount');
                $bidHis = UserBids::where('auction_id', $auction->id)->count();
                return response()->json(['success' => true, 'max_bid' => $max_bid, 'history' => $bidHis ]);
            }
        }
    }

    function auction_cron(){
        $now = Carbon::now('UTC');

        $auctions = Auction::where('status', 1)
            ->whereRaw("DATE_ADD(created_at, interval hour hour) < '$now' ")
            ->get();
        foreach ($auctions as $auction){

            $max_amount = UserBids::where('auction_id',$auction->id )->max('amount');
            $bid = UserBids::where('auction_id', $auction->id)->where('amount',$max_amount)
                ->first();

            if($bid) {

                Auction::whereId($auction->id)->update(['status' => 0]);
                $up =  UserBids::whereId($bid->id)->update(['won' => 1]);
                if ($up) {
                    $user = User::find($bid->user_id);

                    if($user) {

                        $currency = Setting::value('currency');
                        $to = $bid->user_id;
                        $ad_id = $auction->ad_id;

                        $ads = Ads::where('id', $ad_id)->first();

                        if($ads) {

                            $ad_url = url('single/'.urlencode( str_slug($ads->title.'-'.$ads->id) ) );

                            $text = "<strong>Hi ".ucfirst($user->name)."  Your bid accepted</strong><br>";
                            $text.='<strong>Bid price:</strong> '.$currency.' '.$bid->amount;
                            $text.='<a target="_blank" href="'.$ad_url.'"><h3>Ad Title: '.$ads->title.'</h3></a>';

                            $ads_image = AdsImages::where('ad_id', $ad_id)->first();
                            if($ads_image){
                                $path = 'assets/images/listings/'.$ads_image->image;
                                $text .="<img src='".asset($path)."' alt='img'><br>";
                            }
                            $text .= $ads->description;

                            $identifier = Auth::user()->id . ':' . $to;
                            $from = Auth::user()->id;
                            DB::table('message')->insertGetId(['identifier' => $identifier, 'text' => $text, 'from' => $from, 'to' => $to, 'ad_id' => $ad_id]);

                            try {
                                $data = array('content' => $text);
                                Mail::send('emails.mail', $data, function ($msg) use ($user) {
                                    $msg->subject('Your bid accepted');
                                    $msg->to($user->email);
                                });
                            } catch (\Exception $e) {

                            }
                        }
                    }
                    return response()->json(['success' => true, 'message' => 'Bid accepted successfully!']);
                } else {
                    return response()->json(['success' => false]);
                }
            }else{
                return response()->json(['success' => false]);
            }
        }
    }

    function showrooms(Request $request){

        $companies = User::where(['type' => 'c', 'preview' => 1])
            ->paginate(10);
        return view('ads.company', compact('companies'));
    }

    function showrooms_ads($id){
        if ($id !="")
        {
            $user_id = $id;

            $user = User::find($user_id);

            if (!$user)
            {
                return redirect()->back(); exit;
            }
            $result = array();
            $sql_search = Ads::with(array('ad_cf_data' => function ($query)
                {
                    $query->join('customfields', 'customfields.id', '=', 'custom_field_data.cf_id');
                    $query->where('is_shown', 1);
                },
                    'ad_images', 'city', 'category','user'
                )
            )->where('user_id', $user_id);
            $sql_search = $sql_search->where('status', 1);
            $total = $sql_search->count();
            if ($total > 0)
            {
                $sql_search = $sql_search
                ->paginate(10)
                ->appends(request()
                    ->query());

                    $result = $sql_search;
            }

            $ip = $_SERVER['REMOTE_ADDR'];
            DB::table('profile_visit')->insert(['profile_view' => 1, 'ip' => $ip, 'user_id' => $user_id]);
            return view('ads.company_ads', compact('result', 'total', 'user'));
        }
    }
}
