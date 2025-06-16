<?php

namespace Database\Factories;

use App\Models\NewsletterAbonne;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterAbonne>
 */
class NewsletterAbonneFactory extends Factory
{
    protected $model = NewsletterAbonne::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['actif', 'inactif', 'desabonne'];
        
        return [
            'email' => $this->faker->unique()->safeEmail,
            'date_inscription' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'status' => $this->faker->randomElement($statuses, [70, 20, 10]), // 70% actif, 20% inactif, 10% désabonné
            'token_desabonnement' => Str::random(60),
        ];
    }

    /**
     * Indicate that the subscriber is active.
     */
    public function actif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'actif',
        ]);
    }

    /**
     * Indicate that the subscriber is inactive.
     */
    public function inactif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactif',
        ]);
    }

    /**
     * Indicate that the subscriber is unsubscribed.
     */
    public function desabonne(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'desabonne',
        ]);
    }

    /**
     * Indicate that the subscriber is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_inscription' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => 'actif',
        ]);
    }

    /**
     * Indicate that the subscriber is old.
     */
    public function ancien(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_inscription' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
        ]);
    }

    /**
     * Create a subscriber from this month.
     */
    public function ceMois(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_inscription' => $this->faker->dateTimeBetween('first day of this month', 'now'),
            'status' => 'actif',
        ]);
    }

    /**
     * Create a subscriber from this week.
     */
    public function cetteSemaine(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_inscription' => $this->faker->dateTimeBetween('monday this week', 'now'),
            'status' => 'actif',
        ]);
    }

    /**
     * Create a batch of subscribers from different periods.
     */
    public static function createMixedBatch(int $count = 100): array
    {
        $recentCount = (int)($count * 0.3); // 30% récents
        $ancienCount = (int)($count * 0.4);  // 40% anciens
        $moyenCount = $count - $recentCount - $ancienCount; // 30% moyens

        $subscribers = [];

        // Abonnés récents (derniers 3 mois)
        for ($i = 0; $i < $recentCount; $i++) {
            $subscribers[] = static::factory()->recent()->create();
        }

        // Abonnés anciens (plus de 6 mois)
        for ($i = 0; $i < $ancienCount; $i++) {
            $subscribers[] = static::factory()->ancien()->create();
        }

        // Abonnés moyens (3-6 mois)
        for ($i = 0; $i < $moyenCount; $i++) {
            $subscribers[] = static::factory()->state([
                'date_inscription' => fake()->dateTimeBetween('-6 months', '-3 months'),
            ])->create();
        }

        return $subscribers;
    }
} 