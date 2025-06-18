<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use DataTables;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view users');

        $query = User::query()
            ->with('roles')
            ->withCount('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $roleId = $request->input('role');
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $roles = Role::all();

        return view('salesperson-form.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */

   public function saletable()
{
         $salescount = User::role('Sales person')
        ->withCount('customerSales') 
        ->latest()
        ->get();
    $salespersons = User::role('Sales person')->latest()->get();
    return view('salesperson-form.table', compact('salespersons', 'salescount'));
}


    public function editsales(Request $request, $id)
    {
        $this->authorize('edit users');
        $edit = User::find($id);
      return view('salesperson-form.edit', compact('edit'));
    }

   public function updatesales(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|min:6|confirmed',
        'counter_number' => 'required|string|max:255',
    ]);

    $user = User::find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'User not found');
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->counter_number = $request->counter_number;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect()->route('saleperson.table')->with('success', 'User Updated Successfully');
}

public function deleteSalesperson($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
}

    public function store(Request $request)
    {


        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'counter_number' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if (User::where('email', $request->input('email'))->exists()) {
            return redirect()->back()->withInput()
                ->with('error', 'A user with this email already exists.');
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'counter_number' => $request->input('counter_number'),
                'phone' => $request->input('phone'),
            ]);

            // Assign default role: sales-person
            $user->assignRole('Sales person');

            DB::commit();

            return redirect()->route('saleperson.table')
                ->with('success', 'User created successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if ($e->errorInfo[1] == 1062) {
                return redirect()->route('saleperson.table')->with('success', 'Sale Person created successfully!');
            }

            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the user: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view users');

        $user = User::with('roles')->findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit users');

        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Admin role can't have roles modified for security
        if (!$user->hasRole('admin')) {
            $user->syncRoles($request->input('roles', []));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Admin users cannot be deleted');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Verify the specified user's email.
     */
    public function verify(User $user)
    {
        $this->authorize('edit users');

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            return redirect()->back()
                ->with('success', 'User email verified successfully.');
        }

        return redirect()->back()
            ->with('info', 'User email is already verified.');
    }
}
