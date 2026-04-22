<?php

namespace App\Enums;

enum StatusActividadType: string
{
    case Validada = 'Validada';
    case Pendiente = 'Pendiente';
    case Observada = 'Observada';

    public function badge(): string
    {
        return match($this) {
            self::Validada => 'badge-success',
            self::Pendiente => 'badge-neutral',
            self::Observada => 'badge-warning',
        };
    }
}