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
        Schema::table('proyecto_usuario', function (Blueprint $table) {
            $table->enum('estado_invitacion', ['pendiente', 'aceptada', 'rechazada'])->default('aceptada')->after('rol_proyecto');
            $table->string('token_invitacion', 64)->nullable()->unique()->after('estado_invitacion');
            $table->timestamp('fecha_invitacion')->nullable()->after('token_invitacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto_usuario', function (Blueprint $table) {
            $table->dropColumn(['estado_invitacion', 'token_invitacion', 'fecha_invitacion']);
        });
    }
};

