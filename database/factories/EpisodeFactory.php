<?php

namespace Database\Factories;

use App\Models\Episode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    protected $model = Episode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titre = $this->faker->sentence(4, true);
        $types = ['episode', 'coulisse', 'bonus'];
        
        // URLs YouTube d'exemple
        $youtubeUrls = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=oHg5SJYRHA0',
            'https://www.youtube.com/watch?v=SQoA_wjmE9w',
            'https://youtu.be/dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=J9FImc2LOr8',
        ];

        return [
            'titre' => $titre,
            'description' => $this->faker->paragraphs(3, true),
            'youtube_url' => $this->faker->optional(0.8)->randomElement($youtubeUrls),
            'type' => $this->faker->randomElement($types),
            'date_diffusion' => $this->faker->optional(0.9)->dateTimeBetween('-2 years', '+6 months'),
            'slug' => Str::slug($titre) . '-' . $this->faker->unique()->randomNumber(3),
            'is_published' => $this->faker->boolean(80), // 80% de chance d'être publié
        ];
    }

    /**
     * Indicate that the episode is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'date_diffusion' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the episode is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'date_diffusion' => null,
        ]);
    }

    /**
     * Indicate that the episode is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'date_diffusion' => $this->faker->dateTimeBetween('+1 day', '+3 months'),
        ]);
    }

    /**
     * Indicate that the episode is of type 'episode'.
     */
    public function episode(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'episode',
        ]);
    }

    /**
     * Indicate that the episode is of type 'coulisse'.
     */
    public function coulisse(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'coulisse',
        ]);
    }

    /**
     * Indicate that the episode is of type 'bonus'.
     */
    public function bonus(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'bonus',
        ]);
    }

    /**
     * Indicate that the episode has no YouTube URL.
     */
    public function withoutYoutube(): static
    {
        return $this->state(fn (array $attributes) => [
            'youtube_url' => null,
        ]);
    }

    /**
     * Indicate that the episode is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_diffusion' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'is_published' => true,
        ]);
    }
} 