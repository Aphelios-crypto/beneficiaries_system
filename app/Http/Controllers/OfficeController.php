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
    /**
     * Display a listing of all offices from iHRIS (Live View).
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
     * Display a listing of LOCAL offices (Management View).
     */
    public function manage(Request $request)
    {
        if (! Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->query('search', '');
        
        $offices = \App\Models\Office::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('head', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('offices.manage', compact('offices', 'search'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'head' => 'nullable|string|max:255',
            'uuid' => 'nullable|string|max:255',
        ]);

        \App\Models\Office::create($validated);

        return redirect()->route('offices.manage')->with('success', 'Local office created successfully.');
    }

    public function update(Request $request, \App\Models\Office $office)
    {
        if (! Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'head' => 'nullable|string|max:255',
            'uuid' => 'nullable|string|max:255',
        ]);

        $office->update($validated);

        return redirect()->route('offices.manage')->with('success', 'Local office updated successfully.');
    }

    public function destroy(\App\Models\Office $office)
    {
        if (! Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $office->delete();

        return redirect()->route('offices.manage')->with('success', 'Local office deleted successfully.');
    }

    public function sync()
    {
        if (! Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $token = Auth::user()->ihris_token;
        if (!$token) {
            return redirect()->back()->with('error', 'Please login to iHRIS first.');
        }

        $result = $this->ihris->getOffices($token);
        
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        $count = 0;
        foreach ($result['data'] as $officeData) {
            $uuid = $officeData['uuid'] ?? null;
            $name = $officeData['name'] ?? $officeData['office_name'] ?? 'Unknown Office';

            // Find by UUID or Name
            $office = null;
            if ($uuid) {
                $office = \App\Models\Office::where('uuid', $uuid)->first();
            }
            if (!$office) {
                $office = \App\Models\Office::where('name', $name)->first();
            }

            if (!$office) {
                $office = new \App\Models\Office();
            }

            $office->name = $name;
            $office->code = $officeData['code'] ?? $officeData['office_code'] ?? null;
            $office->head = $officeData['head'] ?? $officeData['office_head'] ?? $officeData['head_name'] ?? null;
            $office->uuid = $uuid;
            $office->save();
            $count++;
        }

        return redirect()->route('offices.manage')->with('success', "Synced {$count} offices from iHRIS to local database.");
    }

    /**
     * Fetch API-sourced list of employees assigned to a specific office.
     */
    public function employees(string $id)
    {
        // Try to find local office first to get UUID
        $office = \App\Models\Office::find($id);
        $targetUuid = $office ? $office->uuid : null;
        
        // If passed ID looks like a UUID, use it directly (fallback for API view)
        if (!$targetUuid && (strlen($id) > 10 || str_contains($id, '-'))) {
             $targetUuid = $id;
        }

        $token = Auth::user()->ihris_token;
        if (! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // If we still don't have a UUID, and we are coming from the API view, we might have an API ID.
        // But the previous implementation iterated offices to find UUID. Let's keep that logic for robustness.
        
        if (!$targetUuid) {
             $officesResult = $this->ihris->getOffices($token);
             $offices = $officesResult['data'] ?? [];
             foreach ($offices as $officeApi) {
                if (isset($officeApi['id']) && (string)$officeApi['id'] === (string)$id) {
                    $targetUuid = $officeApi['uuid'] ?? null;
                    break;
                }
             }
        }

        if (!$targetUuid) {
            return response()->json(['employees' => [], 'error' => 'Office UUID not found locally or in API.']);
        }

        // Fetch Employees using the aggregated list (includes DOB and OJTs)
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

        // Filter employees belonging to this office
        $targetOfficeName = null;
        if ($office && $office->name) {
             $targetOfficeName = $office->name;
        } else {
             // Try to get name from API list if not local
             $officesResult = $this->ihris->getOffices($token); // Re-fetch or cache this? Ideally cache, but for now just fetch
             foreach ($officesResult['data'] ?? [] as $o) {
                 if (($o['uuid']??'') === $targetUuid) {
                     $targetOfficeName = $o['name'] ?? $o['office_name'] ?? null;
                     break;
                 }
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
