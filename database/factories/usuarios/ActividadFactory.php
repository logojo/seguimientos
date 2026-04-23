<?php

namespace Database\Factories\Usuarios;

use App\Models\Usuarios\Actividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition(): array
    {
        return [
            'actividad' => $this->faker->sentence(6),
            'objetivo'  => $this->faker->paragraph(2),
            'avance'    => $this->faker->numberBetween(0, 100),
            'status'    => $this->faker->randomElement(['Pendiente', 'Validada', 'Observada']),

            'programa_id'        => $this->faker->randomElement([1, 2]),
            'dependencia_id'     => $this->faker->randomElement([1, 2]),
            'unidad_medida_id'   => $this->faker->randomElement([1, 2]),
            'estrategia_id'      => $this->faker->randomElement([1, 2]),
            'linea_accion_id'    => $this->faker->randomElement([1, 2]),
        ];
    }
}