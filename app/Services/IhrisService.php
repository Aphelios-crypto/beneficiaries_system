<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class IhrisService
{
    protected string $baseUrl;
    protected string $loginEndpoint;

    public function __construct()
    {
        $this->baseUrl      = rtrim(config('ihris.api_base_url'), '/');
        $this->loginEndpoint = config('ihris.login_endpoint');
    }

    /**
     * Attempt to authenticate a user against the iHRIS API.
     *
     * @param  string  $username  (typically the employee's email or username)
     * @param  string  $password
     * @return array{success: bool, token: string|null, user: array|null, message: string}
     */
    public function login(string $username, string $password): array
    {
        $url = $this->baseUrl . $this->loginEndpoint;

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post($url, [
                    'email'    => $username,
                    'password' => $password,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Support both { token: '...' } and { data: { token: '...' } } shapes
                $token = $data['token']
                    ?? $data['access_token']
                    ?? $data['data']['token']
                    ?? $data['data']['access_token']
                    ?? null;

                $user = $data['user']
                    ?? $data['data']['user']
                    ?? $data['data']
                    ?? null;

                if ($token) {
                    return [
                        'success' => true,
                        'token'   => $token,
                        'user'    => $user,
                        'message' => 'Login successful.',
                    ];
                }

                Log::warning('iHRIS login: response OK but no token found.', ['body' => $data]);

                return [
                    'success' => false,
                    'token'   => null,
                    'user'    => null,
                    'message' => 'Authentication succeeded but no token was returned.',
                ];
            }

            // 401 / 422 / etc.
            $body = $response->json();
            $message = $body['message']
                ?? $body['error']
                ?? 'Invalid credentials.';

            Log::info('iHRIS login failed.', [
                'status'  => $response->status(),
                'message' => $message,
            ]);

            return [
                'success' => false,
                'token'   => null,
                'user'    => null,
                'message' => $message,
            ];

        } catch (RequestException $e) {
            Log::error('iHRIS login HTTP error.', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'token'   => null,
                'user'    => null,
                'message' => 'Could not connect to the iHRIS server. Please try again later.',
            ];
        } catch (\Throwable $e) {
            Log::error('iHRIS login unexpected error.', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'token'   => null,
                'user'    => null,
                'message' => 'An unexpected error occurred during authentication.',
            ];
        }
    }

    /**
     * Fetch all employees from the iHRIS API.
     *
     * @param  string  $token  Bearer token obtained from login()
     * @return array{success: bool, data: array, message: string}
     */
    public function getEmployees(string $token): array
    {
        return $this->get('/employees', $token);
    }

    /**
     * Fetch all offices from the iHRIS API.
     *
     * @param  string  $token  Bearer token obtained from login()
     * @return array{success: bool, data: array, message: string}
     */
    public function getOffices(string $token): array
    {
        return $this->get('/offices', $token);
    }

    /**
     * Generic authenticated GET helper.
     *
     * @param  string  $endpoint  Relative endpoint (e.g. '/employees')
     * @param  string  $token
     * @param  array   $query     Optional query parameters
     * @return array{success: bool, data: array, message: string}
     */
    public function get(string $endpoint, string $token, array $query = []): array
    {
        $url = $this->baseUrl . $endpoint;

        try {
            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->get($url, $query);

            if ($response->successful()) {
                $body = $response->json();
                $data = $body['data'] ?? $body;

                return [
                    'success' => true,
                    'data'    => is_array($data) ? $data : [$data],
                    'message' => 'OK',
                ];
            }

            $body    = $response->json();
            $message = $body['message'] ?? $body['error'] ?? 'Request failed.';

            Log::warning("iHRIS GET {$endpoint} failed.", [
                'status'  => $response->status(),
                'message' => $message,
            ]);

            return [
                'success' => false,
                'data'    => [],
                'message' => $message,
            ];

        } catch (\Throwable $e) {
            Log::error("iHRIS GET {$endpoint} error.", ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'data'    => [],
                'message' => 'Could not connect to the iHRIS server.',
            ];
        }
    }
}
