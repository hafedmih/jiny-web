<?php
namespace App\Http\Controllers\boilerplate\Web\TwoFAController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\User;
use App\Providers\RouteServiceProvider;
class TwoFAController extends Controller
{
    /**
     * Write Your Code..
     *
     * @return string
    */
    public function index()
    {
        return view('2fa');
    }
    /**
     * Write Your Code..
     *
     * @return string
    */
    public function store(Request $request)
    {
        $request->validate([
            'code'=>'required',
        ]);
        $find = User::where('id',auth()->user()->id)
                        ->where('otp',$request->code)
                        ->where('otp_expires_at','>=',now()->subMinutes(2))
                        ->first();
        if (!is_null($find)) {
            Session::put('user_2fa',auth()->user()->id);
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        return back()->with('error', 'You entered wrong code.');
    }
    /**
     * Write Your Code..
     *
     * @return string
    */
    public function resend()
    {
        auth()->user()->generateCode();
  
        return back()->with('success', 'We sent you code on your email.');
    }
}