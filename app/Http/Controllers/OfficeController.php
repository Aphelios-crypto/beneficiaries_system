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
}
