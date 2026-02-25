<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->when($request->input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                ]),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users'],
        ]);

        $attributes['password'] = Hash::make(Str::random(32));

        $user = User::create($attributes);

        $status = Password::sendResetLink(
            $request->only('email'),
            static function ($user, $token) {
                $user->notify(new UserCreated($token));
            }
        );

        $redirect = redirect()->route('users.index');

        if ($status !== Password::RESET_LINK_SENT) {
            return $redirect->with('warning', "De gebruiker “<i>{$user->name}</i>” is succesvol toegevoegd, maar er kon geen email gestuurd worden met instructies om een wachtwoord aan te maken. De volgende melding is terug gegeven: <em>".trans($status).'</em>');
        }

        return $redirect->with('success', "De gebruiker “<i>{$user->name}</i>” is succesvol toegevoegd en er een email gestuurd met instructies om een wachtwoord aan te maken.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'id' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->uuid, 'uuid')],
        ]);

        $user->update($attributes);

        return redirect()->route('users.edit', $user)->with('success', "De gebruiker “<i>{$user->name}</i>” is succesvol aangepast!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $userId = auth()->id();
        $deletedUserUuid = $user->uuid;

        $user->delete();

        Log::info("User {$deletedUserUuid} deleted by user {$userId}");

        return redirect()->route('users.index')->with('success', "De gebruiker “<i>{$user->name}</i>” is succesvol verwijderd!");
    }
}
