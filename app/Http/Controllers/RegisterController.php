<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request){
        return view('register');
    }

    public function register(Request $request){
        $firstname   = $request->input('firstname');
        $lastname    = $request->input('lastname');
        $email       = $request->input('email');
        $password    = $request->input('password');
        $countrycode = $request->input('country_code');
        $phonenumber = $request->input('phone_number');
        $languages   = implode(', ', $request->input('languages'));
        $status      = "pending";
        $is_admin    = $request->input('is_admin');

        $user = New User();
        $user->firstname   = $firstname;
        $user->lastname    = $lastname;
        $user->email       = $email;
        $user->password    = Hash::make($password);
        $user->countrycode = $countrycode;
        $user->phonenumber = $phonenumber;
        $user->languages   = $languages;
        $user->status      = $status;
        $user->is_admin    = $is_admin;
        $user->save();

        return redirect()->route('/login');
    }
}
