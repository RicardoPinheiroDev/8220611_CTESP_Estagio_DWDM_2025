<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->sentence(10),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
            'status' => $this->faker->randomElement(['pending', 'paid', 'overdue']),
            'paid_at' => $this->faker->optional(0.3)->dateTime(),
            'invoice_file' => $this->faker->optional(0.7)->filePath(),
            'receipt_file' => $this->faker->optional(0.3)->filePath(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => $this->faker->dateTime(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'paid_at' => null,
            'due_date' => $this->faker->dateTimeBetween('-2 months', '-1 week'),
        ]);
    }
}