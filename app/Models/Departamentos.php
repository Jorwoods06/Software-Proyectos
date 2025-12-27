<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Departamentos extends Model
{
    protected $table = 'departamentos';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'lider_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con usuarios del departamento
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'departamento', 'id');
    }

    /**
     * Relación con el líder del departamento
     */
    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    /**
     * Relación con proyectos del departamento
     */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'departamento_id');
    }

    /**
     * Obtener usuarios activos del departamento
     */
    public function usuariosActivos()
    {
        return $this->usuarios()->where('estado', 'activo');
    }

    /**
     * Obtener departamentos con usuarios activos para formularios
     */
    public static function obtenerConUsuariosActivos()
    {
        return static::with(['usuarios' => function($query) {
            $query->where('estado', 'activo')->orderBy('nombre');
        }])
        ->whereHas('usuarios', function($query) {
            $query->where('estado', 'activo');
        })
        ->orderBy('nombre')
        ->get();
    }
}