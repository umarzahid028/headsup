<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
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
        //$this->authorize('view roles');
        
        $query = Role::withCount('users');
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $roles = $query->paginate(10);
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$this->authorize('create roles');
        
        $permissions = Permission::all();
        $permissionGroups = $this->groupPermissions($permissions);
        
        return view('roles.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$this->authorize('create roles');
        
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            $role = Role::create(['name' => $request->input('name')]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->input('permissions'));
            }
            
            DB::commit();
            
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //$this->authorize('view roles');
        
        $role = Role::findOrFail($id);
        $rolePermissions = $role->permissions;
        
        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //$this->authorize('edit roles');
        
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $permissionGroups = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //$this->authorize('edit roles');
        
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $role = Role::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Check if role can be modified
            if (!in_array($role->name, ['admin'])) {
                $role->name = $request->input('name');
                $role->save();
            }

            // Don't modify admin permissions
            if ($role->name !== 'admin') {
                $role->syncPermissions($request->input('permissions', []));
            }
            
            DB::commit();
            
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating the role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //$this->authorize('delete roles');
        
        $role = Role::findOrFail($id);
        
        // Prevent deletion of system roles
        if (in_array($role->name, ['admin', 'staff', 'vendor'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'This role cannot be deleted');
        }
        
        // Check if users are assigned to this role
        $hasUsers = DB::table('model_has_roles')
            ->where('role_id', $id)
            ->exists();
            
        if ($hasUsers) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'This role is assigned to users and cannot be deleted');
        }
        
        // Delete the role
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }
    
    /**
     * Group permissions by their name prefix (before the first dot).
     */
    private function groupPermissions($permissions)
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $action = $parts[0];
            $resource = implode(' ', array_slice($parts, 1));
            
            if (!isset($groups[$resource])) {
                $groups[$resource] = [];
            }
            
            $groups[$resource][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action
            ];
        }
        
        // Sort groups alphabetically
        ksort($groups);
        
        return $groups;
    }
}
