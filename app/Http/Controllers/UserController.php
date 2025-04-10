<?php
namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('site')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $sites = Site::all();
        return view('users.create', compact('sites'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'site_id' => 'nullable|exists:sites,id',
        ]);

        $person = Person::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        User::create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'person_id' => $person->id,
        ]);

        // Si un site est sélectionné, on l'associe à l'utilisateur
        if ($request->input('site_id')) {
            DB::table('sites')->where('id', $request->input('site_id'))->update([
                'person_id' => $person->id,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $sites = Site::all();
        return view('users.edit', compact('user', 'sites'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'site_id' => 'nullable|exists:sites,id',
        ]);

        $user->update([
            'email' => $request->input('email'),
            'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password,
        ]);

        $user->person->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        // Si un site est sélectionné, on l'associe à l'utilisateur
        if ($request->input('site_id')) {
            DB::table('sites')->where('id', $request->input('site_id'))->update([
                'person_id' => $user->person->id,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Vérifier si l'utilisateur est associé à un site
        if ($user->site) {
            return redirect()->route('users.index')->with('error', 'L\'utilisateur est associé à un site et ne peut pas être supprimé.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}