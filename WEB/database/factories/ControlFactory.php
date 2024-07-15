<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Control>
 */
class ControlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'voltage' => $this->faker->randomFloat(2, 0, 300),
            'power' => $this->faker->randomFloat(2, 0, 900),
            'power_factor' => $this->faker->randomFloat(2, 0, 100),
            'energy' => $this->faker->randomFloat(2, 0, 900),
            'current' => $this->faker->randomFloat(2, 0, 900),
            'biaya' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
