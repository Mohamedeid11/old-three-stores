<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Admin;

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

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    protected function login(Request $request)
    {
        $validateData = $request->validate(
        [
            'user_name'=>'required',
            'password'=>'required'
        ],
        $messages = [
            'user_name.required' => 'Please Enter User Name',
            'password.required' => 'Please Enter Password'
        ]);

        
        $user = Admin::where('user_name', '=', $request->user_name)->where('hide', '=', 0)->first();
    
        if($user === NULL)
        {
            return redirect()->back()->withErrors("Wrong Login Information");
        }
        else if(!Hash::check($request->password, $user->password))
        {
            return redirect()->back()->withErrors("Wrong Login Information");
        }
    
        Auth::guard('admin')->login($user, true);
        return redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }
}
