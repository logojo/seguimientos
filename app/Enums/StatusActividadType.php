<?php

namespace App\Enums;

enum StatusActividadType: string
{
    case Validada = 'Validada';
    case Pendiente = 'Pendiente';
    case Observada = 'Observada';
}