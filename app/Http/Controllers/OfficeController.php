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

        // Fetch employees to find the linking field
        $empResult = $this->ihris->getEmployees($token);
        $employees = $empResult['data'] ?? [];

        if (!empty($employees)) {
            \Illuminate\Support\Facades\Log::info('First Employee Structure:', (array)$employees[0]);
        }

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

        // Attempt 1: Standard RESTful nested resource (common in iHRIS implementations)
        $result = $this->ihris->get("/offices/{$id}/employees", $token);

        return response()->json(['employees' => $result['data'] ?? []]);
    }
}
