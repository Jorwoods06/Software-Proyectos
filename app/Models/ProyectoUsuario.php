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
        'estado_invitacion',
        'token_invitacion',
        'fecha_invitacion',
        'created_at'
    ];

    protected $casts = [
        'fecha_invitacion' => 'datetime',
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

    /**
     * Generar token único para invitación
     */
    public static function generarToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (static::where('token_invitacion', $token)->exists());
        
        return $token;
    }

    /**
     * Crear invitación pendiente
     */
    public static function crearInvitacion(int $proyectoId, int $userId, string $rol): self
    {
        return static::create([
            'proyecto_id' => $proyectoId,
            'user_id' => $userId,
            'rol_proyecto' => $rol,
            'estado_invitacion' => 'pendiente',
            'token_invitacion' => static::generarToken(),
            'fecha_invitacion' => now(),
        ]);
    }

    /**
     * Aceptar invitación
     */
    public function aceptar(): bool
    {
        return $this->update([
            'estado_invitacion' => 'aceptada',
        ]);
    }

    /**
     * Rechazar invitación
     */
    public function rechazar(): bool
    {
        return $this->update([
            'estado_invitacion' => 'rechazada',
        ]);
    }

    /**
     * Verificar si la invitación está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado_invitacion === 'pendiente';
    }

    /**
     * Verificar si la invitación está aceptada
     */
    public function estaAceptada(): bool
    {
        return $this->estado_invitacion === 'aceptada';
    }

    /**
     * Buscar invitación por token
     */
    public static function buscarPorToken(string $token): ?self
    {
        return static::where('token_invitacion', $token)->first();
    }
}
