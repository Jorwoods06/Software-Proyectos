<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trazabilidad extends Model
{
    protected $table = 'trazabilidad';
    public $timestamps = true;

    const CREATED_AT = 'fecha'; // Laravel usará esta columna
    const UPDATED_AT = null;    // No hay updated_at

    protected $fillable = [
        'proyecto_id',
        'user_id',
        'accion',
        'detalle',
        'fecha'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
