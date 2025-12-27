<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoUserPermiso extends Model
{
    protected $table = 'proyecto_user_permiso';
    public $timestamps = false;
    protected $fillable = ['proyecto_id','user_id','permiso_id','tipo'];

    // relaciones
    public function permiso()
    {
        return $this->belongsTo(Permisos::class, 'permiso_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
