<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('departamento_id');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
        
        // Migrar datos: obtener el creador del proyecto desde proyecto_usuario donde rol_proyecto = 'lider'
        DB::statement("UPDATE proyectos p 
            INNER JOIN proyecto_usuario pu ON p.id = pu.proyecto_id 
            SET p.created_by = pu.user_id 
            WHERE pu.rol_proyecto = 'lider'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};

