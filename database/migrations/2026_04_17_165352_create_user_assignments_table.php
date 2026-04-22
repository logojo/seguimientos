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
        Schema::connection('base')->create('usuarios.user_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            //$table->foreignId('municipio_id')->nullable()->constrained('catalogos.cat_municipios');
            $table->string('perfil');
            $table->foreignId('assigned_by')->constrained('users');
            $table->string('cargo')->nullable();
            $table->timestamp('assigned_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'revoked_at']);
            //$table->index(['municipio_id', 'assigned_at']);
            $table->index(['perfil', 'assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('base')->dropIfExists('usuarios.user_assignments');
    }
};
