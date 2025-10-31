<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends BaseController
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->first();

        if (! $user) {
            abort(404, 'User not found!');
        }

        $otp = random_int(100000, 999999);

        if ($request->has('email')) {
            $this->emailService->sendEmail(
                $request->email,
                'Password Reset OTP',
                6478497,
                [
                    'otp' => $otp,
                    'name' => $user->name ?? 'User',
                ]
            );
        }

        // dd($otp, $user);

        DB::beginTransaction();

        $user->update([
            'otp' => Hash::make($otp),
            'email_verified_at' => null,
            'otp_sent_at' => now(),
            'status' => 1,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'OTP sent successfully!',
        ]);
    }

    public function create_password(ResetPasswordRequest $request)
    {
        $user = User::where('id', Auth::id())->first();
        $user->update(['password' => Hash::make($request->new_password)]);

        return 'Password changed successfully!';
    }

    public function change_password(ChangePasswordRequest $request)
    {
        User::find(Auth::id())
            ->update(['password' => Hash::make($request->new_password), 'status' => 0]);

        return User::find(Auth::id());
    }
}
