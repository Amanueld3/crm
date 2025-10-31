<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OTPController extends BaseController
{
    public function verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required:email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            abort(404, 'User not found!');
        }

        if ($user->email_verified_at != null) {
            abort(400, 'User already verified.');
        }

        if (Hash::check($request['otp'], $user->otp)) {

            $to = Carbon::parse($user->otp_sent_at);

            if ($to->diffInMinutes(Carbon::now()) > 15) {
                abort(400, 'The code you entered has expired. Please resend code to verify your number.');
            }

            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);

            $token = Auth::guard('api')->login($user);

            return response()
                ->json([
                    'message' => 'User verified successfully',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ], 201);
        } else {
            abort(400, 'Your otp is not correct!');
        }
    }

    public function resend_otp(Request $request)
    {
        $otp = random_int(100000, 999999);
        $user = User::where('phone', $request->phone)->first();

        if ($user->email_verified_at !== null) {
            abort(400, 'User already verified!');
        }

        if (! empty($user)) {

            $this->sendSMS($request->phone, $otp, $request->appKey);

            $user->update([
                'otp' => Hash::make($otp),
                'email_verified_at' => null,
                'otp_sent_at' => now(),
            ]);

            return response()->json([
                'message' => 'OTP sent successfully!',
            ]);
        } else {
            abort(404, 'User not found!');
        }
    }
}
