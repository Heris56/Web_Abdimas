<?php

namespace Database\Factories;
use App\Models\ActivityLogs;
use App\Models\StaffKeuangan;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLogs>
 */
class ActivityLogsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ActivityLogs::class;

    public function definition(): array
    {
        return [
            'user_id' => StaffKeuangan::inRandomOrder()->value('id') ?? null,
            'action' => $this->faker->randomElement(['create', 'update', 'delete']),
            'table_name' => $this->faker->word(),
            'record_id' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(),
            'old_values' => ['field1' => $this->faker->word(), 'field2' => $this->faker->word()],
            'new_values' => ['field1' => $this->faker->word(), 'field2' => $this->faker->word()],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
