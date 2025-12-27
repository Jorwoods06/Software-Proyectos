<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['nombre', 'descripcion'];
    public $timestamps = true;

    // 🔹 Relación con permisos
    public function permisos()
{
    return $this->belongsToMany(
        Permisos::class,   // Modelo de permisos
        'rol_permiso',     // Nombre de la tabla pivote
        'rol_id',          // FK hacia roles en la tabla pivote
        'permiso_id'       // FK hacia permisos en la tabla pivote
        
    );
}


    // 🔹 Relación con usuarios
    public function users()
    {
        // Igual, en la tabla pivote definiste "user_role"
        // donde va "user_id" y "role_id"
        return $this->belongsToMany(
            User::class,
            'user_role',
            'role_id',  // FK en pivote hacia roles
            'user_id'   // FK en pivote hacia users
        );
    }
}
