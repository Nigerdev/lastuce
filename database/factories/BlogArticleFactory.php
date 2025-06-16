<?php

namespace Database\Factories;

use App\Models\BlogArticle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogArticle>
 */
class BlogArticleFactory extends Factory
{
    protected $model = BlogArticle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titre = $this->faker->sentence(6, true);
        $contenu = $this->faker->paragraphs(8, true);
        $extrait = Str::limit(strip_tags($contenu), 160);
        
        // Titres d'articles de blog spécifiques au contenu
        $titresSpecifiques = [
            'Les coulisses du tournage de notre dernier épisode',
            'Comment nous sélectionnons les astuces à présenter',
            'Rencontre avec l\'équipe technique de L\'Astuce',
            'Les défis de la production d\'une émission télé',
            'Nos astuces préférées qui n\'ont pas été retenues',
            'L\'évolution de L\'Astuce au fil des années',
            'Les réactions les plus mémorables de nos téléspectateurs',
            'Comment adapter le tournage aux contraintes sanitaires',
            'Les moments drôles qui n\'apparaissent pas à l\'écran',
            'L\'importance du feedback de notre communauté',
        ];

        return [
            'titre' => $this->faker->optional(0.7)->randomElement($titresSpecifiques) ?: $titre,
            'contenu' => $contenu,
            'image' => $this->faker->optional(0.6)->imageUrl(800, 600, 'business', true, 'Faker'),
            'slug' => Str::slug($titre) . '-' . $this->faker->unique()->randomNumber(3),
            'date_publication' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', '+3 months'),
            'is_published' => $this->faker->boolean(75), // 75% de chance d'être publié
            'extrait' => $extrait,
            'meta_description' => Str::limit($extrait, 160),
        ];
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'date_publication' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the article is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'date_publication' => null,
        ]);
    }

    /**
     * Indicate that the article is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'date_publication' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
        ]);
    }

    /**
     * Indicate that the article is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'date_publication' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the article has a featured image.
     */
    public function withImage(): static
    {
        $imageTopics = ['business', 'technics', 'food', 'nature', 'people'];
        
        return $this->state(fn (array $attributes) => [
            'image' => $this->faker->imageUrl(800, 600, $this->faker->randomElement($imageTopics), true, 'Blog'),
        ]);
    }

    /**
     * Indicate that the article has no image.
     */
    public function withoutImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => null,
        ]);
    }

    /**
     * Create an article with long content.
     */
    public function longContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'contenu' => $this->faker->paragraphs(15, true),
        ]);
    }

    /**
     * Create an article with short content.
     */
    public function shortContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'contenu' => $this->faker->paragraphs(3, true),
        ]);
    }

    /**
     * Create an article from a specific month.
     */
    public function fromMonth(int $year, int $month): static
    {
        return $this->state(fn (array $attributes) => [
            'date_publication' => $this->faker->dateTimeBetween(
                "{$year}-{$month}-01", 
                "{$year}-{$month}-" . cal_days_in_month(CAL_GREGORIAN, $month, $year)
            ),
            'is_published' => true,
        ]);
    }

    /**
     * Create an article about behind the scenes.
     */
    public function coulisses(): static
    {
        $titresCoulisses = [
            'Dans les coulisses de notre studio',
            'Les préparatifs avant le tournage',
            'Comment nous testons les astuces',
            'L\'équipe derrière les caméras',
            'Les moments de pause entre les prises',
        ];

        $contenuCoulisses = [
            "Aujourd'hui, nous vous emmenons dans les coulisses de notre émission pour découvrir comment nous préparons chaque épisode.",
            "Le tournage d'un épisode de L'Astuce demande plusieurs heures de préparation en amont.",
            "Notre équipe technique travaille en permanence pour vous offrir la meilleure qualité d'image et de son.",
        ];

        return $this->state(fn (array $attributes) => [
            'titre' => $this->faker->randomElement($titresCoulisses),
            'contenu' => $this->faker->randomElement($contenuCoulisses) . "\n\n" . $this->faker->paragraphs(6, true),
        ]);
    }

    /**
     * Create popular articles.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'date_publication' => $this->faker->dateTimeBetween('-3 months', '-1 month'),
        ]);
    }
} 