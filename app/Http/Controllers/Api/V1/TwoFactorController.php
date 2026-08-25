<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function enable(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->hasTwoFactorEnabled()) {
                return response()->json([
                    'message' => '2FA is already enabled'
                ], 422);
            }

            $secret = $this->google2fa->generateSecretKey();
            $user->setTwoFactorSecret($secret);

            // Generate QR code URL
            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            return response()->json([
                'message' => '2FA enabled successfully',
                'data' => [
                    'secret' => $secret,
                    'qr_code_url' => $qrCodeUrl,
                    'recovery_codes' => $this->generateRecoveryCodes(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function verify(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|size:6',
            ]);

            $user = Auth::user();
            
            if (!$user->getTwoFactorSecret()) {
                return response()->json([
                    'message' => '2FA is not enabled'
                ], 422);
            }

            $valid = $this->google2fa->verifyKey(
                $user->getTwoFactorSecret(),
                $request->code
            );

            if (!$valid) {
                return response()->json([
                    'message' => 'Invalid verification code'
                ], 422);
            }

            $user->two_factor_confirmed_at = now();
            $user->save();

            return response()->json([
                'message' => '2FA verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function disable(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasTwoFactorEnabled()) {
                return response()->json([
                    'message' => '2FA is not enabled'
                ], 422);
            }

            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->two_factor_confirmed_at = null;
            $user->save();

            return response()->json([
                'message' => '2FA disabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        return $codes;
    }
}
