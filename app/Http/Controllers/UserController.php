<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Users/Index', [
            'users'   => User::query()
                ->when($request->input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($user) => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ]),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name'     => 'required',
            'email'    => ['required', 'email', 'unique:users'],
            'password' => 'required',
        ]);

        User::create($attributes);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $attributes = $request->validate([
            'name'  => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($attributes);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }
}
