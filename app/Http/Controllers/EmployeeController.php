<?php

namespace App\Http\Controllers;

use App\Services\IhrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct(protected IhrisService $ihris) {}

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
        ]);

        $token = Auth::user()->ihris_token;
        if (!$token) {
            return back()->with('error', 'You are not connected to iHRIS.');
        }

        $result = $this->ihris->createEmployee($token, $request->only([
            'first_name', 'middle_name', 'last_name', 'extension', 'email'
        ]));

        if ($result['success']) {
            return back()->with('success', 'Employee created successfully in iHRIS.');
        }

        return back()->withErrors(['api' => $result['message']]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
        ]);

        $token = Auth::user()->ihris_token;
        if (!$token) {
            return back()->with('error', 'You are not connected to iHRIS.');
        }

        $result = $this->ihris->updateEmployee($token, $id, $request->only([
            'first_name', 'middle_name', 'last_name', 'extension', 'email'
        ]));

        if ($result['success']) {
            return back()->with('success', 'Employee updated successfully in iHRIS.');
        }

        return back()->withErrors(['api' => $result['message']]);
    }

    public function destroy(string $id)
    {
        $token = Auth::user()->ihris_token;
        if (!$token) {
            return back()->with('error', 'You are not connected to iHRIS.');
        }

        $result = $this->ihris->deleteEmployee($token, $id);

        if ($result['success']) {
            return back()->with('success', 'Employee deleted successfully from iHRIS.');
        }

        return back()->with('error', $result['message']);
    }
}
