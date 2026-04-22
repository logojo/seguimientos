<?php

namespace App\Services;

use App\Models\User;
use App\Models\Usuarios\UserAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserAssigmentService
{

    /**
     * Asigna un usuario a una municipio/refugio/perfil.
     * Revoca automáticamente la asignación activa anterior.
     */
    public function assign(
        User     $user,
        //?int     $municipio_id,
        Role     $perfil,
        User     $assignedBy,
        ?string  $cargo
    ): UserAssignment {

        return DB::transaction(function () use ($user, $perfil, $assignedBy, $cargo) {

            // 1. Revocar asignación activa actual
            UserAssignment::forUser($user->id)
                ->active()
                ->update(['revoked_at' => now()]);

            // 2. Sincronizar rol en Spatie Permission
            $user->syncRoles([$perfil->name]);

            // 3. Crear nueva asignación
            $assignment = UserAssignment::create([
                'user_id'       => $user->id,
                //'municipio_id'  => $municipio_id,
                'perfil'        => $perfil->name,
                'assigned_by'   => $assignedBy->id,
                'assigned_at'   => now(),
                'revoked_at'    => null,
                'cargo'         => $cargo,
            ]);


            return $assignment->load(['municipio', 'refugio', ]);
        });
    }

    /**
     * Revoca la asignación activa de un usuario.
     */
    public function revoke(User $user, User $revokedBy): bool
    {
        $revocados = UserAssignment::forUser($user->id)
            ->active()
            ->update([
                'revoked_at' => now(),
            ]);


        return $revocados > 0;
    }

    /**
     * Retorna la asignación activa actual del usuario.
     */
    public function currentAssignment(User $user): ?UserAssignment
    {
        return UserAssignment::forUser($user->id)
            ->active()
            ->with(['municipio', 'refugio'])
            ->latest('assigned_at')
            ->first();
    }

    /**
     * Retorna la asignación vigente en una fecha específica.
     */
    public function assignmentAt(User $user, Carbon $fecha): ?UserAssignment
    {
        return UserAssignment::forUser($user->id)
            ->activeAt($fecha)
            ->with(['municipio', 'refugio'])
            ->latest('assigned_at')
            ->first();
    }

    /**
     * Historial completo de asignaciones del usuario.
     */
    public function history(User $user)
    {
        return UserAssignment::forUser($user->id)
            ->with(['municipio', 'refugio', 'assignedBy'])
            ->orderByDesc('assigned_at')
            ->get();
    }


}