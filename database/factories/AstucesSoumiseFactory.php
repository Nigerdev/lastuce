<?php

namespace Database\Factories;

use App\Models\AstucesSoumise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AstucesSoumise>
 */
class AstucesSoumiseFactory extends Factory
{
    protected $model = AstucesSoumise::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['en_attente', 'approuve', 'rejete'];
        
        // Titres d'astuces créatifs
        $titresAstuces = [
            'Comment économiser sur ses courses alimentaires',
            'Astuce pour nettoyer efficacement sa maison',
            'Méthode pour organiser son temps au quotidien',
            'Technique de cuisine pour débutants',
            'Conseil pour mieux dormir naturellement',
            'Astuce jardinage pour avoir de belles plantes',
            'Méthode de rangement qui fonctionne vraiment',
            'Technique pour apprendre plus rapidement',
            'Conseil beauté fait maison économique',
            'Astuce DIY pour décorer sa maison',
        ];

        return [
            'nom' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'titre_astuce' => $this->faker->randomElement($titresAstuces),
            'description' => $this->faker->paragraphs(
                $this->faker->numberBetween(2, 5), 
                true
            ),
            'fichier_joint' => $this->faker->optional(0.3)->word . '.pdf',
            'status' => $this->faker->randomElement($statuses),
            'commentaires_admin' => $this->faker->optional(0.4)->sentence(),
        ];
    }

    /**
     * Indicate that the astuce is pending.
     */
    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_attente',
            'commentaires_admin' => null,
        ]);
    }

    /**
     * Indicate that the astuce is approved.
     */
    public function approuve(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approuve',
            'commentaires_admin' => $this->faker->optional(0.7)->sentence(),
        ]);
    }

    /**
     * Indicate that the astuce is rejected.
     */
    public function rejete(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejete',
            'commentaires_admin' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the astuce has an attachment.
     */
    public function withAttachment(): static
    {
        $extensions = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        
        return $this->state(fn (array $attributes) => [
            'fichier_joint' => $this->faker->word . '.' . $this->faker->randomElement($extensions),
        ]);
    }

    /**
     * Indicate that the astuce has no attachment.
     */
    public function withoutAttachment(): static
    {
        return $this->state(fn (array $attributes) => [
            'fichier_joint' => null,
        ]);
    }

    /**
     * Indicate that the astuce is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create an astuce with admin comments.
     */
    public function withAdminComments(): static
    {
        $comments = [
            'Excellente astuce, très utile pour nos lecteurs !',
            'Merci pour cette contribution, nous allons la publier.',
            'Cette astuce nécessite quelques ajustements avant publication.',
            'Contenu intéressant mais déjà traité dans un précédent épisode.',
            'Astuce validée par notre équipe, merci !',
        ];

        return $this->state(fn (array $attributes) => [
            'commentaires_admin' => $this->faker->randomElement($comments),
        ]);
    }
} 