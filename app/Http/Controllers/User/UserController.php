<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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
            'name'  => 'required',
            'email' => ['required', 'email', 'unique:users'],
        ]);

        $attributes['password'] = Hash::make(Str::random(32));

        User::create($attributes);

        $status = Password::sendResetLink(
            $request->only('email'),
            static function ($user, $token) {
                $user->notify(new UserCreated($token));
            }
        );

        $redirect = redirect()->route('users.index');

        if ($status !== Password::RESET_LINK_SENT) {
            return $redirect->with('warning', 'De gebruiker “<i>{$user->name}</i>” is succesvol toegevoegd, maar er kon geen email gestuurd worden met instructies om een wachtwoord aan te maken. De volgende melding is terug gegeven: <em>' . trans($status) . '</em>');
        }

        return $redirect->with('success', 'De gebruiker “<i>{$user->name}</i>” is succesvol toegevoegd en er een email gestuurd met instructies om een wachtwoord aan te maken.');
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

        return redirect()->route('users.index')->with('success', 'De gebruiker “<i>{$user->name}</i>” is succesvol aangepast!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', "De gebruiker “<i>{$user->name}</i>” is succesvol verwijderd!");
    }
}
