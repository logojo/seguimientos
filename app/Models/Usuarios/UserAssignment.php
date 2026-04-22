<?php

namespace App\Models\Usuarios;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAssignment extends Model
{
    protected $connection = 'base';
    protected $table = 'usuarios.user_assignments';

    protected $fillable = [
        'user_id',
        //'municipio_id',
        'perfil',
        'assigned_by',
        'assigned_at',
        'revoked_at',
        'cargo',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'revoked_at'  => 'datetime',
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //todo: definir que datos tendra esta tabla 
    // public function municipio(): BelongsTo
    // {
    //     return $this->belongsTo(CatMunicipios::class, 'municipio_id');
    // }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }


    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEntidad(Builder $query, int $entidadId): Builder
    {
        return $query->where('entidad_id', $entidadId);
    }

    public function scopeForRefugio(Builder $query, int $refugioId): Builder
    {
        return $query->where('refugio_id', $refugioId);
    }

    // Asignaciones vigentes en una fecha específica
    public function scopeActiveAt(Builder $query, Carbon $fecha): Builder
    {
        return $query
            ->where('assigned_at', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->whereNull('revoked_at')
                  ->orWhere('revoked_at', '>=', $fecha);
            });
    }


    public function isActive(): bool
    {
        return is_null($this->revoked_at);
    }

    public function duracion(): string
    {
        $fin = $this->revoked_at ?? now();
        return $this->assigned_at->diffForHumans($fin, true);
    }
}
