<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permisos;
use App\Models\Departamentos;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /** 🟩 Listar usuarios */
    public function index()
    {
        $users = User::with(['roles', 'departamento'])->paginate(10);
        return view('users.index', compact('users'));
    }

    /** 🟩 Formulario de creación */
    public function create()
    {
        $roles = Role::all();
        $departamentos = Departamentos::all();
        return view('users.create', compact('roles', 'departamentos'));
    }

    /** 🟩 Guardar nuevo usuario */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6|confirmed',
            'departamento' => 'required|exists:departamentos,id',
            'estado'       => 'required|in:activo,inactivo',
            'roles'        => 'required|array',
            'roles.*'      => 'exists:roles,id',
        ]);

        $this->userService->crearUsuarioConRolYPermisos($validated);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente con roles asignados.');
    }

    /** 🟩 Formulario de edición */
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        $departamentos = Departamentos::all();

        return view('users.edit', compact('user', 'roles', 'departamentos'));
    }

    /** 🟩 Actualizar usuario */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email'  => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'departamento' => 'required|exists:departamentos,id',
            'roles' => 'array'
        ]);

        $this->userService->actualizarDatosBasicos($user, $validated);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    /** 🟩 Eliminar usuario */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (session()->has('user_id') && session('user_id') == $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario');
        }

        $this->userService->eliminarUsuario($user);

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }

    /** 🟩 Vista de gestión de roles y permisos */
    public function manageRolesAndPermissions($id)
    {
        $user = User::with(['roles', 'permisosDirectos'])->findOrFail($id);
        $roles = Role::with('permisos')->get();
        $permisos = Permisos::all();

        return view('users.manage', compact('user', 'roles', 'permisos'));
    }

    /** 🟩 Actualizar roles y permisos */
    public function updateRolesAndPermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->userService->actualizarRolesYPermisos(
            $user,
            $request->input('roles', []),
            $request->input('permisos', []),
            $request->input('tipos', [])
        );

        return redirect()->route('users.manage', $user->id)
                         ->with('success', 'Roles y permisos actualizados correctamente.');
    }

    /** 🟩 Duplicar roles y permisos entre usuarios */
    public function duplicateRolesAndPermissions(Request $request)
    {
        $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_id'   => 'required|exists:users,id',
        ]);

        $from = User::with(['roles', 'permisosDirectos'])->findOrFail($request->from_user_id);
        $to   = User::findOrFail($request->to_user_id);

        $this->userService->duplicarRolesYPermisos($from, $to);

        return redirect()->route('users.index')
                         ->with('success', 'Roles y permisos duplicados correctamente.');
    }

    public function duplicateForm()
    {
        $users = User::with(['roles', 'permisosDirectos'])->get();
        $roles = Role::with('permisos')->get();
        $permisos = Permisos::all();

        return view('users.duplicate', compact('users', 'roles', 'permisos'));
    }
}
