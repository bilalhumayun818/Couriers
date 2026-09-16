<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'Admin', 403);

        return view('settings.users', ['users' => User::orderBy('name')->paginate(20)]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->role === 'Admin', 403);
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['Admin', 'User'])],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);
        User::create($data);

        return redirect()->route('settings.users')->with('success', 'User created successfully. They can now sign in.');
    }
}
