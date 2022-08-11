<?php

namespace App\Http\Controllers\Auth;

use App\Ads;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user-panel';

    //app\Http\Auth\LoginController
    protected function authenticated($request, $user)
    {
      if ($user->status == 0) {
                  $message = 'This account been blocked, please contact us for more information!';
                  Auth::logout($request);
                  return back()->with('error',$message)->withInput($request->only('email'));
              }

        if (!Auth::guest())
        {
            User::whereId(Auth::user()->id)->update(['is_login' => 1]);
            Ads::where('user_id', Auth::user()->id)->update(['is_login' => 1]);
        }

        if ($user->type == 'adm')
        {
            return redirect('/admin');
        }else{
            return redirect('user-panel');
        }
    }
    // logout user
    public function logout(Request $request)
    {
        User::whereId(Auth::user()->id)->update(['is_login' => 0]);
        Ads::where('user_id', Auth::user()->id)->update(['is_login' => 0]);
        $this->guard()->logout();
        $request->session()->invalidate();

        return redirect('/');
    }

    public function redirectToFBProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $socialUser = Socialite::driver('facebook')->user();


        // check old user
        $old_user = User::where('email', $socialUser->email)->first();
        if($old_user){
            Auth::login($old_user);
            return redirect('/home');
        }else{

            $user = new User();

            $user->name = $socialUser->name;
            $user->email = $socialUser->email;
            $user->password = bcrypt(12345678);

            if($user->save()){
                Auth::login($user);
                return redirect('/home');
            }
        }
    }

    public function handleGoogleProviderCallback()
    {
        $socialUser = Socialite::driver('google')->stateless()->user();

        // check old user
        $old_user = User::where('email', $socialUser->email)->first();
        if($old_user){
            Auth::login($old_user);
            return redirect('/home');
        }else{

            $user = new User();

            $user->name = $socialUser->user['given_name'].' '.$socialUser->user['family_name'];;

            $user->email = $socialUser->email;
            $user->image = $socialUser->avatar;
            //$user->password = bcrypt(12345678);

            if($user->save()){
                Auth::login($user);
                return redirect('/home');
            }
        }
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
