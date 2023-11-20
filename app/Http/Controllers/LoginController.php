<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\IncomeExpense;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm(Request $request){
        return view('login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            if ($user->is_admin == 'True') {
                // dd('sdsd');
                $users = User::all();
                return view('admin', compact('users'));
            } else {
                if ($user->approved) {
                    $user = Auth::user();
                    $user_id = $user->id;

                    $monthlyIncomeData = IncomeExpense::where('user_id', $user_id)->where('type', 'income')->get();

                    $expenseData = IncomeExpense::where('user_id',$user_id)->where('type','expense')->get();

                    return redirect()->route('dashboard')->with([
                        'user' => $user,
                        'incomeData' => $monthlyIncomeData,
                        'expenseData' => $expenseData
                    ]);
                } else {
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your account is pending approval. Please wait for admin approval.',
                    ]);
                }
            }
        } else {
            return redirect()->route('login')->withErrors([
                'email' => 'Invalid credentials',
            ]);
        }
    }


    // public function login(Request $request){
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();

    //         if ($user->is_admin) {
    //             if($user->approved){
    //                 $users = User::all(); 
    //                 return view('admin', compact('users'));
    //             }else{
    //                 return redirect()->route('login')->withErrors([
    //                     'email' => 'Your account is pending approval. Please wait for admin approval.',
    //                 ]);
    //             }
    //         } else {
    //             return view('dashboard');
    //         }
    //     } else {
    //         return redirect()->route('login')->withErrors([
    //             'email' => 'Invalid credentials',
    //         ]);
    //     }
    // }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

}
