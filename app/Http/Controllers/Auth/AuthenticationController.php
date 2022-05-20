<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthenticationController extends Controller
{
    /**
     * 
     * show login page for user login
     * 
     * @param NULL
     * 
     * @return view
     * 
     */

    public function showLoginPage()
    {

        if(Auth::check()){
            return redirect()->route('admin.dashboard');
        }else{
            return view('auth.login');
        }
    }

    /**
     * 
     * user login
     * 
     * @param $request
     * 
     * @return Boolen
     * 
     */

    public function login(Request $request)
    {
        $login_credential =  $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user =  User::where('email', $request->email)->first();

        $user =  User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Session::flash('error', 'credentials does not match our records!');
            return redirect()->back();
        } else {

            $user = User::where('email', $request->email)->first();
            Auth::login($user);
            // $request->session()->regenerate();
            Session::flash('success', 'Login successfull!');
            return redirect()->route('admin.dashboard');
        }
    }

    /**
     * 
     * show register page for user regisre
     * 
     * @param  NULL
     * 
     * @return view
     * 
     */

    public function show_register_page()
    {

        return view('auth.register');
    }

    /**
     * 
     * user registraion process
     * 
     * @param  $request
     * 
     * @return reidrect dashboard
     * 
     */

    public function register(Request $request)
    {
        $regsiter_credential =  $request->validate([

            'name' => 'required',
            'user_email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = new User();
        $user->user_name = $request->name;
        $user->user_email = $request->user_email;
        $user->password = Hash::make($request->password);
        $user->save();
        Session::flash('message', 'registraion successfull');
        return redirect('/login');
    }
    /**
     * 
     * user profile
     * 
     * @param  NULL
     * 
     * @return user object
     * 
     */

    public function profile()
    {
    }

    /**
     * 
     * user logout
     * 
     * @param  $request
     * 
     * @return reidrect login page
     * 
     */

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
