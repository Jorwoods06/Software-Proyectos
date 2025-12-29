<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notificacion_config', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->string('valor', 255);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar valores por defecto
        DB::table('notificacion_config')->insert([
            [
                'clave' => 'horas_antes_vencimiento',
                'valor' => '24',
                'descripcion' => 'Horas antes de la fecha de vencimiento para enviar notificación de tarea próxima a vencer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'clave' => 'notificar_admin_tareas_vencidas',
                'valor' => '1',
                'descripcion' => 'Notificar a administradores cuando hay tareas vencidas (1 = sí, 0 = no)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacion_config');
    }
};

