<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\IhrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(protected IhrisService $ihris) {}

    /**
     * Display a listing of all users (local DB) with their roles,
     * and show iHRIS employee data alongside.
     */
    public function index(Request $request)
    {
        $search  = $request->query('search', '');
        $empPage = max(1, (int) $request->query('emp_page', 1));
        $perPage = 15;

        // Local users with roles
        $query = User::with('roles')->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        // iHRIS employees (paginated)
        $token     = Auth::user()->ihris_token;
        $employees = [];
        $empTotal  = 0;
        $empPages  = 1;
        $apiError  = null;

        if ($token) {
            $cacheKey = 'ihris_employees_aggregated';
            $result   = \Illuminate\Support\Facades\Cache::get($cacheKey);

            if (! $result) {
                $result = $this->ihris->getAllEmployeesIncludingOjts($token);
                if ($result['success'] && !empty($result['data'])) {
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $result, 600);
                }
            }

            $allEmps   = $result['data'] ?? [];
            $apiError  = $result['success'] ? null : $result['message'];

            // Filter employees by search query
            if ($search) {
                $lowerSearch = strtolower($search);
                $allEmps = array_filter($allEmps, function ($emp) use ($lowerSearch) {
                    $haystack = strtolower(
                        ($emp['name'] ?? '') . ' ' .
                        ($emp['first_name'] ?? '') . ' ' .
                        ($emp['middle_name'] ?? '') . ' ' .
                        ($emp['last_name'] ?? '') . ' ' .
                        ($emp['surname'] ?? '') . ' ' .
                        ($emp['extension'] ?? '') . ' ' .
                        ($emp['email'] ?? '') . ' ' .
                        ($emp['id'] ?? '') . ' ' .
                        ($emp['employee_id'] ?? '')
                    );
                    return str_contains($haystack, $lowerSearch);
                });
            }

            $empTotal  = count($allEmps);
            $empPages  = (int) ceil($empTotal / $perPage);
            $empPage   = min($empPage, max(1, $empPages));
            $employees = array_slice($allEmps, ($empPage - 1) * $perPage, $perPage);
        }

        $roles = Role::where('guard_name', 'web')->get();

        return view('users.index', compact(
            'users', 'employees', 'roles', 'search', 'apiError',
            'empPage', 'empPages', 'empTotal', 'perPage'
        ));
    }

    /**
     * Update the role of a local user.
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Remove all existing roles and assign the new one
        $user->syncRoles([$request->role]);

        return back()->with('success', "Role updated to \"{$request->role}\" for {$user->name}.");
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password, // Casts to hashed automatically in User model
        ]);

        $user->assignRole($request->role);

        return back()->with('success', "User \"{$user->name}\" created successfully.");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'string', 'exists:roles,name'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();
        $user->syncRoles([$request->role]);

        return back()->with('success', "User \"{$user->name}\" updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', "User \"{$user->name}\" deleted successfully.");
    }
}
