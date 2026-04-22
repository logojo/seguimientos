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
        Schema::connection('base')->create('usuarios.bitacoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('accion');
            $table->string('campo')->nullable();
            $table->string('valor_anterior')->nullable();
            $table->string('valor_actual')->nullable();
            $table->morphs('storeable');
            $table->foreignId('user_assignment_id')->constrained('usuarios.user_assignments');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('base')->dropIfExists('usuarios.bitacoras');
    }
};
