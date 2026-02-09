<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.accounts.index', compact('users'));
    }

    public function create()
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_id' => 'required|integer|unique:users',
            'password' => 'required|string|min:4', // Passcode
            'role' => ['required', Rule::in(['admin', 'cashier', 'employee'])],
            'permissions' => 'nullable|array',
        ]);

        User::create([
            'name' => $validated['name'],
            'account_id' => $validated['account_id'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? [],
            'email' => null, // Email is optional/nullable
        ]);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(User $account)
    {
        return view('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, User $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_id' => ['required', 'integer', Rule::unique('users')->ignore($account->id)],
            'password' => 'nullable|string|min:4',
            'role' => ['required', Rule::in(['admin', 'cashier', 'employee'])],
            'permissions' => 'nullable|array',
        ]);

        $account->name = $validated['name'];
        $account->account_id = $validated['account_id'];
        $account->role = $validated['role'];
        $account->permissions = $validated['permissions'] ?? [];

        if (!empty($validated['password'])) {
            $account->password = Hash::make($validated['password']);
        }

        $account->save();

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(User $account)
    {
        if ($account->id === auth()->id()) {
             return back()->with('error', 'You cannot delete your own account.');
        }
        
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
