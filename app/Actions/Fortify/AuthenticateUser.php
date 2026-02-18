<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\IhrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
    public function __construct(protected IhrisService $ihris) {}

    /**
     * Authenticate the incoming request.
     *
     * When STAND_ALONE_MODE is false (default), the user's credentials are
     * validated against the iHRIS API first. On success a local User record
     * is upserted so that Fortify / Jetstream can manage the session normally.
     *
     * When STAND_ALONE_MODE is true, standard local-DB authentication is used.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User|null
     */
    public function __invoke(Request $request): ?User
    {
        $email    = $request->input(Fortify::username());   // 'email' by default
        $password = $request->input('password');

        // ── Stand-alone mode: fall back to local password check ──────────────
        if (config('ihris.stand_alone_mode')) {
            return $this->authenticateLocally($email, $password);
        }

        // ── API mode: authenticate against iHRIS ─────────────────────────────
        $result = $this->ihris->login($email, $password);

        if (! $result['success']) {
            Log::info('iHRIS authentication failed for user.', ['email' => $email]);
            return null;
        }

        // Upsert the local user record so Fortify can create a session
        $apiUser = $result['user'] ?? [];

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'          => $apiUser['name']
                    ?? $apiUser['full_name']
                    ?? $apiUser['employee_name']
                    ?? $email,
                // Store a hashed version of the password so stand-alone mode
                // still works if the API is temporarily unreachable.
                'password'      => Hash::make($password),
                'ihris_token'   => $result['token'],
                'ihris_user_id' => $apiUser['id'] ?? $apiUser['employee_id'] ?? null,
                'is_api_user'   => true,
            ]
        );

        // Assign default role if not already assigned
        if (! $user->hasAnyRole(['Super Admin', 'Admin', 'Employee'])) {
            $defaultRole = config('ihris.default_api_user_role', 'Employee');
            $user->assignRole($defaultRole);
        }

        return $user;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function authenticateLocally(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }
}
