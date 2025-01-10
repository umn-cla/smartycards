<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    protected function createTextBlock(string $text): array
    {
        return [
            'type' => 'text',
            'content' => $text,
        ];
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'front' => [
                $this->createTextBlock($this->faker->sentence),
            ],
            'back' => [
                $this->createTextBlock($this->faker->sentence),
            ],

        ];
    }
}
