<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       // Define standard request driver mapping directly
        Auth::viaRequest('sanctum', function (Request $request) {
            
            // 1. Check if token exists in Authorization Header
            $token = $request->bearerToken();

            // 2. Fallback: If no header token is present, grab it from the cookie
            if (!$token && $request->hasCookie('access_token')) {
                $token = $request->cookie('access_token');
            }

            if ($token) {
                // 3. Decode '%7C' safely into a literal pipe character '|'
                $cleanToken = urldecode($token);

                // 4. Look up the token record in the database
                $accessToken = PersonalAccessToken::findToken($cleanToken);

                // 5. If valid token exists, return the user model instance
                if ($accessToken && $accessToken->tokenable) {
                    return $accessToken->tokenable->withAccessToken($accessToken);
                }
            }

            return null; // Return null to trigger the 401 Unauthenticated redirect
        });
    }
}
