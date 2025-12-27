<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    protected $table = 'comentarios';
    
    protected $fillable = [
        'tarea_id',
        'user_id',
        'comentario'
    ];

    // La tabla tiene created_at pero no updated_at
    public $timestamps = true;
    const UPDATED_AT = null; // Deshabilitar updated_at

    /**
     * Relación con la tarea
     */
    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    /**
     * Relación con el usuario que hizo el comentario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

