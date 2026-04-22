<?php

use App\Enums\StatusActividadType;
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
        Schema::connection('base')->create('usuarios.actividades', function (Blueprint $table) {
            $table->id();
            $table->string('actividad');
            $table->string('objetivo');
            $table->double('avance', 2);
            $table->enum('status', array_column(StatusActividadType::cases(), 'value'));

            $table->foreignId('user_assignment_id')->constrained('usuarios.user_assignments');
            $table->foreignId('programa_id')->constrained('catalogos.cat_programas');
            $table->foreignId('dependencia_id')->constrained('catalogos.cat_dependencias');
            $table->foreignId('unidad_medida_id')->constrained('catalogos.cat_unidades_medida');
            $table->foreignId('estrategia_id')->constrained('catalogos.cat_estrategias');
            $table->foreignId('linea_accion_id')->constrained('catalogos.cat_lineas_accion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('base')->dropIfExists('usuarios.actividades');
    }
};
