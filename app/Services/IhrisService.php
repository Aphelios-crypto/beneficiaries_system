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
        // Wrapper for the new generic send method
        return $this->send('get', $endpoint, $token, $query);
    }

    /**
     * Generic authenticated GET helper. (Deprecated, use send instead)
     *
     * @param  string  $endpoint  Relative endpoint (e.g. '/employees')
     * @param  string  $token
     * @param  array   $query     Optional query parameters
     * @return array{success: bool, data: array, message: string}
     */
    public function oldGet(string $endpoint, string $token, array $query = []): array
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
    /**
     * Create a new employee in iHRIS.
     *
     * @param  string  $token
     * @param  array   $data
     * @return array{success: bool, message: string, data?: array}
     */
    public function createEmployee(string $token, array $data): array
    {
        return $this->send('post', '/employees', $token, $data);
    }

    /**
     * Update an existing employee in iHRIS.
     *
     * @param  string  $token
     * @param  string  $id
     * @param  array   $data
     * @return array{success: bool, message: string, data?: array}
     */
    public function updateEmployee(string $token, string $id, array $data): array
    {
        return $this->send('put', "/employees/{$id}", $token, $data);
    }

    /**
     * Delete an employee from iHRIS.
     *
     * @param  string  $token
     * @param  string  $id
     * @return array{success: bool, message: string}
     */
    public function deleteEmployee(string $token, string $id): array
    {
        return $this->send('delete', "/employees/{$id}", $token);
    }

    /**
     * Generic authenticated request helper.
     *
     * @param  string  $method    get|post|put|delete
     * @param  string  $endpoint  Relative endpoint
     * @param  string  $token
     * @param  array   $data      Body or Query parameters
     * @return array
     */
    public function send(string $method, string $endpoint, string $token, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        try {
            $request = Http::timeout(30)
                ->withToken($token)
                ->acceptJson();

            $response = match (strtolower($method)) {
                'post'   => $request->post($url, $data),
                'put'    => $request->put($url, $data),
                'delete' => $request->delete($url, $data),
                default  => $request->get($url, $data),
            };

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'data'    => $body['data'] ?? $body,
                    'message' => 'Success',
                ];
            }

            $body    = $response->json();
            $message = $body['message'] ?? $body['error'] ?? 'Request failed.';

            Log::warning("iHRIS {$method} {$endpoint} failed.", [
                'status'  => $response->status(),
                'message' => $message,
                'data'    => $data
            ]);

            return [
                'success' => false,
                'message' => $message,
            ];

        } catch (\Throwable $e) {
            Log::error("iHRIS {$method} {$endpoint} error.", ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Could not connect to the iHRIS server.',
            ];
        }
    }

    /**
     * Fetch all employees from ALL offices using the special endpoint /all-employees/office/{uuid}.
     * Merges global list (for DOB) with office lists (for OJTs and richer office data).
     *
     * @param  string  $token
     * @return array{success: bool, data: array, message: string}
     */
    public function getAllEmployeesIncludingOjts(string $token): array
    {
        // 1. Fetch Global Employees
        $globalResult = $this->getEmployees($token);
        $globalEmps = $globalResult['data'] ?? [];
        
        $allEmployees = [];
        foreach ($globalEmps as $emp) {
            $key = $emp['uuid'] ?? $emp['id'] ?? uniqid();
            
            // Fix: If dob is 1970-01-01, it is placeholder data for empty
            if (isset($emp['dob']) && $emp['dob'] === '1970-01-01') {
                $emp['dob'] = null;
            }
            
            $allEmployees[$key] = $emp;
        }

        // 2. Fetch Offices
        $officesResult = $this->getOffices($token);
        $offices = $officesResult['data'] ?? [];
        $uuids = array_filter(array_column($offices, 'uuid'));
        
        if (empty($uuids)) {
             return [
                'success' => true,
                'data'    => array_values($allEmployees),
                'message' => 'Returning global employees only.',
            ];
        }

        // 3. Parallel Fetching for Office Staff (includes OJTs)
        $baseUrl = $this->baseUrl;
        $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($uuids, $token, $baseUrl) {
            $reqs = [];
            foreach ($uuids as $uuid) {
                $reqs[] = $pool->withToken($token)->acceptJson()->get("{$baseUrl}/all-employees/office/{$uuid}");
            }
            return $reqs;
        });

        // 4. Merge Office Data
        foreach ($responses as $resp) {
            if ($resp->successful()) {
                $data = $resp->json();
                if (!is_array($data)) continue;

                foreach ($data as $emp) {
                    $key = $emp['uuid'] ?? $emp['id'] ?? uniqid();
                    
                    if (isset($allEmployees[$key])) {
                        // Merge and preserve global DOB if office record lacks it
                        $allEmployees[$key] = array_merge($allEmployees[$key], $emp);
                    } else {
                        // Potential OJT
                        $allEmployees[$key] = $emp;
                    }
                }
            }
        }

        return [
            'success' => true,
            'data'    => array_values($allEmployees),
            'message' => 'Successfully aggregated and merged employee details.',
        ];
    }
}
