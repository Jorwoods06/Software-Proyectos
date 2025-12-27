<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoUsuario extends Model
{
    protected $table = 'proyecto_usuario';
    public $timestamps = true;

    protected $fillable = [
        'proyecto_id',
        'user_id',
        'rol_proyecto',
        'created_at'
    ];

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
