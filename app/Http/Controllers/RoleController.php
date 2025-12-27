<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permisos;

class RoleController extends Controller
{
    public function create()
{
    return view('roles.create'); // Formulario para crear un nuevo rol
}

public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|unique:roles,nombre',
    ]);

    $rol = Role::create(['nombre' => $request->nombre]);

    // Redirige a la misma página con mensaje de éxito en sesión
    return redirect()->back()->with('success', 'Rol creado exitosamente.');
}



public function permissions()
{
    $roles = Role::with('permisos')->get();
    $permisos = Permisos::all();

    return view('roles.permissions', compact('roles', 'permisos'));
}


public function updatePermissions(Request $request)
{
    $rol = Role::find($request->role_id);
    $rol->permisos()->sync($request->permisos ?? []);

    // Redirige a la misma página con mensaje de éxito en sesión
    return redirect()->back()->with('success', 'Permisos actualizados correctamente.');
}


}
