<?php

namespace App\Http\Controllers;

use App\Services\IhrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeController extends Controller
{
    public function __construct(protected IhrisService $ihris) {}

    /**
     * Display a listing of all offices from iHRIS.
     */
    public function index(Request $request)
    {
        $token  = Auth::user()->ihris_token;
        $search = $request->query('search', '');

        if (! $token) {
            return view('offices.index', [
                'offices' => [],
                'error'   => 'No iHRIS token found. Please log out and log in again.',
                'search'  => $search,
            ]);
        }

        $result = $this->ihris->getOffices($token);

        $offices = $result['data'] ?? [];

        // Client-side search filter
        if ($search) {
            $offices = array_filter($offices, function ($office) use ($search) {
                $name = strtolower($office['name'] ?? $office['office_name'] ?? '');
                return str_contains($name, strtolower($search));
            });
        }

        return view('offices.index', [
            'offices' => array_values($offices),
            'error'   => $result['success'] ? null : $result['message'],
            'search'  => $search,
        ]);
    }

    /**
     * Fetch API-sourced list of employees assigned to a specific office.
     */
    public function employees(string $id)
    {
        $token = \Illuminate\Support\Facades\Auth::user()->ihris_token;
        if (! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 1. Get Target Office UUID
        // We need the office UUID to query the specific endpoint
        $officesResult = $this->ihris->getOffices($token);
        $offices = $officesResult['data'] ?? [];
        $targetUuid = null;
        
        foreach ($offices as $office) {
            // Check ID first
            if (isset($office['id']) && (string)$office['id'] === (string)$id) {
                $targetUuid = $office['uuid'] ?? null;
                break;
            }
            // Fallback to checking if ID passed IS the UUID
            if (($office['uuid'] ?? '') === $id) {
                $targetUuid = $office['uuid'];
                break;
            }
        }

        if (!$targetUuid) {
            return response()->json(['employees' => [], 'error' => 'Office not found']);
        }

        // 2. Fetch Employees using the aggregated list (includes DOB and OJTs)
        $cacheKey = 'ihris_employees_aggregated';
        $result   = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (! $result) {
            $result = $this->ihris->getAllEmployeesIncludingOjts($token);
            if ($result['success'] && !empty($result['data'])) {
                \Illuminate\Support\Facades\Cache::put($cacheKey, $result, 600);
            }
        }

        if (!$result['success']) {
            return response()->json(['employees' => [], 'error' => $result['message']]);
        }

        // 3. Filter employees belonging to this office
        $targetOfficeName = null;
        foreach ($offices as $office) {
            if (($office['uuid'] ?? '') === $targetUuid) {
                $targetOfficeName = $office['name'] ?? $office['office_name'] ?? null;
                break;
            }
        }

        $allEmps = $result['data'] ?? [];
        $officeEmps = array_filter($allEmps, function ($emp) use ($targetOfficeName, $targetUuid) {
            $empOfficeName = $emp['office_name'] ?? $emp['office'] ?? '';
            $empOfficeUuid = $emp['office_uuid'] ?? null;
            
            return ($targetOfficeName && stripos($empOfficeName, $targetOfficeName) !== false)
                || ($empOfficeUuid === $targetUuid);
        });

        return response()->json(['employees' => array_values($officeEmps)]);
    }
}
