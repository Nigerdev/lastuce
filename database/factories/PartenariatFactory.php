<?php

namespace Database\Factories;

use App\Models\Partenariat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partenariat>
 */
class PartenariatFactory extends Factory
{
    protected $model = Partenariat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['nouveau', 'en_cours', 'accepte', 'refuse'];
        
        // Noms d'entreprises fictives
        $entreprises = [
            'TechStart Solutions',
            'Green Living Co.',
            'Urban Lifestyle',
            'Smart Home Innovations',
            'EcoFriendly Products',
            'Digital Marketing Plus',
            'Creative Design Studio',
            'Wellness & Health',
            'Food & Gourmet',
            'Fashion Forward',
            'Travel Adventures',
            'Home & Garden Expert',
        ];

        // Messages de partenariat types
        $messagesTypes = [
            "Bonjour, nous aimerions proposer un partenariat avec votre émission. Nos produits correspondent parfaitement à votre audience.",
            "Nous sommes une entreprise spécialisée dans les solutions écologiques et nous pensons que cela pourrait intéresser vos téléspectateurs.",
            "Notre marque souhaiterait collaborer avec L'Astuce pour présenter nos innovations aux téléspectateurs.",
            "Nous avons développé des produits qui pourraient faire l'objet d'astuces intéressantes dans votre émission.",
            "En tant que fans de votre émission, nous aimerions vous proposer un partenariat commercial avantageux.",
        ];

        return [
            'nom_entreprise' => $this->faker->randomElement($entreprises),
            'contact' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => $this->faker->unique()->companyEmail,
            'message' => $this->faker->randomElement($messagesTypes) . "\n\n" . 
                        $this->faker->paragraphs(2, true),
            'status' => $this->faker->randomElement($statuses),
            'notes_internes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the partnership is new.
     */
    public function nouveau(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nouveau',
            'notes_internes' => null,
        ]);
    }

    /**
     * Indicate that the partnership is in progress.
     */
    public function enCours(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_cours',
            'notes_internes' => $this->faker->optional(0.8)->sentence(),
        ]);
    }

    /**
     * Indicate that the partnership is accepted.
     */
    public function accepte(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepte',
            'notes_internes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the partnership is refused.
     */
    public function refuse(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refuse',
            'notes_internes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the partnership is active (nouveau or en_cours).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $this->faker->randomElement(['nouveau', 'en_cours']),
        ]);
    }

    /**
     * Indicate that the partnership is processed (accepte or refuse).
     */
    public function traite(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $this->faker->randomElement(['accepte', 'refuse']),
            'notes_internes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Create a partnership with detailed internal notes.
     */
    public function withDetailedNotes(): static
    {
        $notes = [
            '[15/01/2024] Premier contact établi avec l\'entreprise.',
            '[20/01/2024] Réunion prévue la semaine prochaine pour discuter des détails.',
            '[25/01/2024] Proposition intéressante mais budget à revoir.',
            '[30/01/2024] Accord trouvé sur les conditions du partenariat.',
            '[05/02/2024] Contrat en cours de finalisation.',
        ];

        return $this->state(fn (array $attributes) => [
            'notes_internes' => $this->faker->randomElements($notes, $this->faker->numberBetween(1, 3))
                ? implode("\n", $this->faker->randomElements($notes, $this->faker->numberBetween(1, 3)))
                : $this->faker->sentence(),
        ]);
    }

    /**
     * Create a recent partnership request.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => $this->faker->randomElement(['nouveau', 'en_cours']),
        ]);
    }
} 