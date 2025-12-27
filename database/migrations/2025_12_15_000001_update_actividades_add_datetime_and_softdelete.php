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
        // Actualizar enum de estado para incluir 'eliminado'
        DB::statement("ALTER TABLE `actividades` MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','finalizado','eliminado') DEFAULT 'pendiente'");
        
        // Agregar campos de fecha/hora como datetime
        Schema::table('actividades', function (Blueprint $table) {
            // Renombrar campos de fecha a fecha_inicio_old y fecha_fin_old temporalmente
            // Luego crear nuevos campos datetime y migrar datos
        });
        
        // Migrar datos existentes y cambiar tipos
        DB::statement("ALTER TABLE `actividades` 
            CHANGE COLUMN `fecha_inicio` `fecha_inicio_old` DATE NULL,
            CHANGE COLUMN `fecha_fin` `fecha_fin_old` DATE NULL,
            ADD COLUMN `fecha_inicio` DATETIME NULL AFTER `descripcion`,
            ADD COLUMN `fecha_fin` DATETIME NULL AFTER `fecha_inicio`");
        
        // Migrar datos: convertir fechas a datetime (añadir hora 00:00:00)
        DB::statement("UPDATE `actividades` SET 
            `fecha_inicio` = CONCAT(`fecha_inicio_old`, ' 00:00:00') 
            WHERE `fecha_inicio_old` IS NOT NULL");
        
        DB::statement("UPDATE `actividades` SET 
            `fecha_fin` = CONCAT(`fecha_fin_old`, ' 23:59:59') 
            WHERE `fecha_fin_old` IS NOT NULL");
        
        // Eliminar columnas temporales
        DB::statement("ALTER TABLE `actividades` 
            DROP COLUMN `fecha_inicio_old`,
            DROP COLUMN `fecha_fin_old`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios
        DB::statement("ALTER TABLE `actividades` 
            CHANGE COLUMN `fecha_inicio` `fecha_inicio_old` DATETIME NULL,
            CHANGE COLUMN `fecha_fin` `fecha_fin_old` DATETIME NULL,
            ADD COLUMN `fecha_inicio` DATE NULL AFTER `descripcion`,
            ADD COLUMN `fecha_fin` DATE NULL AFTER `fecha_inicio`");
        
        // Convertir datetime a date (solo la fecha)
        DB::statement("UPDATE `actividades` SET 
            `fecha_inicio` = DATE(`fecha_inicio_old`) 
            WHERE `fecha_inicio_old` IS NOT NULL");
        
        DB::statement("UPDATE `actividades` SET 
            `fecha_fin` = DATE(`fecha_fin_old`) 
            WHERE `fecha_fin_old` IS NOT NULL");
        
        // Eliminar columnas temporales
        DB::statement("ALTER TABLE `actividades` 
            DROP COLUMN `fecha_inicio_old`,
            DROP COLUMN `fecha_fin_old`");
        
        // Revertir enum
        DB::statement("ALTER TABLE `actividades` MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','finalizado') DEFAULT 'pendiente'");
    }
};

