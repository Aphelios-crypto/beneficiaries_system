<?php

namespace App\Http\Controllers;

use App\Services\IhrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $ihrisService;

    public function __construct(IhrisService $ihrisService)
    {
        $this->ihrisService = $ihrisService;
    }

    public function index()
    {
        $user = Auth::user();
        $officesCount = '—';

        if ($user && $user->ihris_token) {
            try {
                $response = $this->ihrisService->getOffices($user->ihris_token);

                if ($response['success'] && is_array($response['data'])) {
                    $officesCount = count($response['data']);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch offices count for dashboard: ' . $e->getMessage());
            }
        }

        return view('dashboard', compact('officesCount'));
    }
}
