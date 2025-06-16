<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterAbonne;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class NewsletterController extends Controller
{
    /**
     * Affichage du formulaire d'inscription
     */
    public function create()
    {
        // Statistiques pour encourager l'inscription
        $stats = [
            'subscribers' => NewsletterAbonne::actif()->count(),
            'growth_rate' => '+15%',
            'satisfaction' => '94%'
        ];

        // Avantages de l'inscription
        $benefits = [
            [
                'icon' => 'mail',
                'title' => __('Astuces exclusives'),
                'description' => __('Recevez des astuces inédites chaque semaine directement dans votre boîte mail.')
            ],
            [
                'icon' => 'star',
                'title' => __('Contenu premium'),
                'description' => __('Accédez à du contenu réservé aux abonnés et à des bonus exclusifs.')
            ],
            [
                'icon' => 'gift',
                'title' => __('Offres spéciales'),
                'description' => __('Bénéficiez d\'offres partenaires et de réductions exclusives.')
            ],
            [
                'icon' => 'bell',
                'title' => __('Nouveautés en avant-première'),
                'description' => __('Soyez informé en premier des nouveaux épisodes et projets.')
            ]
        ];

        // Exemple de contenu newsletter
        $sampleContent = [
            'subject' => __('🎯 5 astuces pour organiser votre semaine'),
            'preview' => __('Cette semaine, découvrez comment optimiser votre planning avec des méthodes simples mais efficaces...'),
            'sections' => [
                __('L\'astuce de la semaine'),
                __('Coulisses des tournages'),
                __('Recommandations produits'),
                __('Réponses à vos questions')
            ]
        ];

        return view('pages.newsletter.create', compact(
            'stats',
            'benefits',
            'sampleContent'
        ));
    }

    /**
     * Traitement de l'inscription
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:newsletter_abonnes,email',
            'prenom' => 'nullable|string|max:100',
            'nom' => 'nullable|string|max:100',
            'interets' => 'nullable|array',
            'interets.*' => 'string|in:cuisine,menage,organisation,beaute,technologie,lifestyle',
            'frequency' => 'required|in:hebdomadaire,bihebdomadaire,mensuel',
            'source' => 'nullable|string|max:100',
            'agree_terms' => 'required|accepted',
            'g-recaptcha-response' => 'nullable|string' // Si vous utilisez reCAPTCHA
        ], [
            'email.required' => __('L\'adresse email est obligatoire'),
            'email.email' => __('Format d\'email invalide'),
            'email.unique' => __('Cette adresse email est déjà inscrite à notre newsletter'),
            'frequency.required' => __('Veuillez choisir une fréquence de réception'),
            'frequency.in' => __('Fréquence invalide'),
            'agree_terms.required' => __('Vous devez accepter nos conditions d\'utilisation')
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('Veuillez corriger les erreurs dans le formulaire.'));
        }

        try {
            // Vérification anti-spam
            if ($this->isSpam($request)) {
                return redirect()->back()
                    ->with('error', __('Inscription détectée comme spam. Veuillez réessayer.'))
                    ->withInput();
            }

            // Vérification reCAPTCHA (optionnel)
            if ($request->filled('g-recaptcha-response')) {
                if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                    return redirect()->back()
                        ->with('error', __('Vérification reCAPTCHA échouée. Veuillez réessayer.'))
                        ->withInput();
                }
            }

            // Génération du token unique
            $token = $this->generateUniqueToken();

            // Création de l'abonnement
            $abonne = NewsletterAbonne::create([
                'email' => strtolower(trim($request->email)),
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'interets' => $request->interets ? json_encode($request->interets) : null,
                'frequence_envoi' => $request->frequency,
                'source_inscription' => $request->source ?: 'site_web',
                'token_desinscription' => $token,
                'status' => NewsletterAbonne::STATUS_ACTIF,
                'ip_inscription' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'date_inscription' => now(),
                'confirme' => false // Nécessitera une confirmation par email
            ]);

            // Intégration avec service email (Mailchimp, etc.)
            $this->syncWithEmailService($abonne);

            // Envoi de l'email de confirmation
            $this->sendConfirmationEmail($abonne);

            return redirect()->route('newsletter.success')
                ->with('success', __('Inscription réussie ! Vérifiez votre email pour confirmer votre abonnement.'))
                ->with('email', $abonne->email);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') { // Erreur de duplication
                return redirect()->back()
                    ->with('error', __('Cette adresse email est déjà inscrite.'))
                    ->withInput();
            }
            
            \Log::error('Erreur base de données inscription newsletter: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Erreur technique. Veuillez réessayer.'))
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'inscription newsletter: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue. Veuillez réessayer.'))
                ->withInput();
        }
    }

    /**
     * Page de confirmation d'inscription
     */
    public function success()
    {
        if (!session()->has('success') || !session()->has('email')) {
            return redirect()->route('newsletter.subscribe');
        }

        $email = session('email');

        // Prochaines étapes
        $nextSteps = [
            [
                'step' => 1,
                'title' => __('Vérifiez votre email'),
                'description' => __('Un email de confirmation a été envoyé à') . ' ' . $email,
                'action' => __('Cliquez sur le lien de confirmation')
            ],
            [
                'step' => 2,
                'title' => __('Ajoutez-nous à vos contacts'),
                'description' => __('Pour ne jamais rater nos emails'),
                'action' => __('Ajoutez newsletter@lastuce.com à vos contacts')
            ],
            [
                'step' => 3,
                'title' => __('Première newsletter'),
                'description' => __('Vous recevrez votre première newsletter'),
                'action' => __('Dans les 7 prochains jours')
            ]
        ];

        // Suggestions en attendant
        $suggestions = [
            [
                'title' => __('Découvrez nos derniers épisodes'),
                'link' => \App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'),
                'icon' => 'play'
            ],
            [
                'title' => __('Lisez notre blog'),
                'link' => \App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index'),
                'icon' => 'book'
            ],
            [
                'title' => __('Suivez-nous sur les réseaux'),
                'link' => '#social',
                'icon' => 'share'
            ]
        ];

        return view('pages.newsletter.success', compact(
            'email',
            'nextSteps',
            'suggestions'
        ));
    }

    /**
     * Confirmation d'email
     */
    public function confirm($token)
    {
        try {
            $abonne = NewsletterAbonne::where('token_desinscription', $token)
                ->where('confirme', false)
                ->firstOrFail();

            $abonne->update([
                'confirme' => true,
                'date_confirmation' => now()
            ]);

            // Sync avec le service email
            $this->syncWithEmailService($abonne, 'confirm');

            // Email de bienvenue
            $this->sendWelcomeEmail($abonne);

            return view('pages.newsletter.confirmed', compact('abonne'))
                ->with('success', __('Votre inscription a été confirmée avec succès !'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return view('pages.newsletter.error')
                ->with('error', __('Lien de confirmation invalide ou expiré.'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la confirmation newsletter: ' . $e->getMessage());
            
            return view('pages.newsletter.error')
                ->with('error', __('Une erreur est survenue lors de la confirmation.'));
        }
    }

    /**
     * Désinscription
     */
    public function unsubscribe(Request $request, $token)
    {
        try {
            $abonne = NewsletterAbonne::where('token_desinscription', $token)->firstOrFail();

            if ($request->isMethod('GET')) {
                // Afficher la page de confirmation de désinscription
                return view('pages.newsletter.unsubscribe', compact('abonne'));
            }

            // Traiter la désinscription
            $reason = $request->input('reason', 'non_specifie');
            $feedback = $request->input('feedback');

            $abonne->update([
                'status' => NewsletterAbonne::STATUS_DESINSCRIT,
                'date_desinscription' => now(),
                'raison_desinscription' => $reason,
                'commentaire_desinscription' => $feedback
            ]);

            // Sync avec le service email
            $this->syncWithEmailService($abonne, 'unsubscribe');

            // Email de confirmation de désinscription
            $this->sendUnsubscribeConfirmation($abonne);

            return view('pages.newsletter.unsubscribed', compact('abonne'))
                ->with('success', __('Vous avez été désinscrit avec succès.'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return view('pages.newsletter.error')
                ->with('error', __('Lien de désinscription invalide.'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la désinscription: ' . $e->getMessage());
            
            return view('pages.newsletter.error')
                ->with('error', __('Une erreur est survenue lors de la désinscription.'));
        }
    }

    /**
     * Gestion des préférences d'abonnement
     */
    public function preferences($token)
    {
        try {
            $abonne = NewsletterAbonne::where('token_desinscription', $token)->firstOrFail();

            $interetsDisponibles = [
                'cuisine' => __('Cuisine et alimentation'),
                'menage' => __('Ménage et nettoyage'),
                'organisation' => __('Organisation'),
                'beaute' => __('Beauté et bien-être'),
                'technologie' => __('Technologie'),
                'lifestyle' => __('Lifestyle')
            ];

            $frequences = [
                'hebdomadaire' => __('Hebdomadaire'),
                'bihebdomadaire' => __('Toutes les 2 semaines'),
                'mensuel' => __('Mensuel')
            ];

            return view('pages.newsletter.preferences', compact(
                'abonne',
                'interetsDisponibles',
                'frequences'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return view('pages.newsletter.error')
                ->with('error', __('Lien invalide.'));
        }
    }

    /**
     * Mise à jour des préférences
     */
    public function updatePreferences(Request $request, $token)
    {
        $request->validate([
            'prenom' => 'nullable|string|max:100',
            'nom' => 'nullable|string|max:100',
            'interets' => 'nullable|array',
            'interets.*' => 'string|in:cuisine,menage,organisation,beaute,technologie,lifestyle',
            'frequence_envoi' => 'required|in:hebdomadaire,bihebdomadaire,mensuel'
        ]);

        try {
            $abonne = NewsletterAbonne::where('token_desinscription', $token)->firstOrFail();

            $abonne->update([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'interets' => $request->interets ? json_encode($request->interets) : null,
                'frequence_envoi' => $request->frequence_envoi
            ]);

            // Sync avec le service email
            $this->syncWithEmailService($abonne, 'update');

            return redirect()->back()
                ->with('success', __('Vos préférences ont été mises à jour avec succès.'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', __('Lien invalide.'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour des préférences: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue lors de la mise à jour.'));
        }
    }

    /**
     * API pour l'inscription rapide (AJAX)
     */
    public function quickSubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_abonnes,email'
        ]);

        try {
            $token = $this->generateUniqueToken();

            $abonne = NewsletterAbonne::create([
                'email' => strtolower(trim($request->email)),
                'frequence_envoi' => 'hebdomadaire',
                'source_inscription' => 'inscription_rapide',
                'token_desinscription' => $token,
                'status' => NewsletterAbonne::STATUS_ACTIF,
                'ip_inscription' => $request->ip(),
                'date_inscription' => now(),
                'confirme' => false
            ]);

            $this->sendConfirmationEmail($abonne);

            return response()->json([
                'success' => true,
                'message' => __('Inscription réussie ! Vérifiez votre email.')
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => __('Cette adresse email est déjà inscrite.')
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur inscription rapide newsletter: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Une erreur est survenue.')
            ], 500);
        }
    }

    /**
     * Méthodes privées
     */

    private function generateUniqueToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (NewsletterAbonne::where('token_desinscription', $token)->exists());

        return $token;
    }

    private function isSpam(Request $request): bool
    {
        // Limite par IP
        $recentSubscriptions = NewsletterAbonne::where('ip_inscription', $request->ip())
            ->where('date_inscription', '>=', now()->subHour())
            ->count();

        return $recentSubscriptions >= 5;
    }

    private function verifyRecaptcha(string $response): bool
    {
        $secretKey = config('services.recaptcha.secret');
        
        if (!$secretKey) {
            return true; // Pas de vérification si pas configuré
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => request()->ip()
            ]);

            $data = $response->json();
            return $data['success'] ?? false;

        } catch (\Exception $e) {
            \Log::error('Erreur vérification reCAPTCHA: ' . $e->getMessage());
            return false;
        }
    }

    private function syncWithEmailService(NewsletterAbonne $abonne, string $action = 'subscribe'): void
    {
        try {
            $service = config('newsletter.service', 'none'); // mailchimp, sendinblue, etc.
            
            if ($service === 'none') {
                return;
            }

            // Ici vous pouvez intégrer avec votre service d'emailing
            // Exemple pour Mailchimp:
            // $this->mailchimpService->sync($abonne, $action);
            
        } catch (\Exception $e) {
            \Log::error("Erreur sync service email ($action): " . $e->getMessage());
        }
    }

    private function sendConfirmationEmail(NewsletterAbonne $abonne): void
    {
        try {
            Mail::send('emails.newsletter-confirmation', compact('abonne'), function ($message) use ($abonne) {
                $message->to($abonne->email, $abonne->prenom_complet)
                        ->subject(__('Confirmez votre inscription à la newsletter L\'Astuce'));
            });
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email confirmation newsletter: ' . $e->getMessage());
        }
    }

    private function sendWelcomeEmail(NewsletterAbonne $abonne): void
    {
        try {
            Mail::send('emails.newsletter-bienvenue', compact('abonne'), function ($message) use ($abonne) {
                $message->to($abonne->email, $abonne->prenom_complet)
                        ->subject(__('Bienvenue dans la communauté L\'Astuce !'));
            });
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email bienvenue newsletter: ' . $e->getMessage());
        }
    }

    private function sendUnsubscribeConfirmation(NewsletterAbonne $abonne): void
    {
        try {
            Mail::send('emails.newsletter-desinscription', compact('abonne'), function ($message) use ($abonne) {
                $message->to($abonne->email, $abonne->prenom_complet)
                        ->subject(__('Confirmation de désinscription - L\'Astuce'));
            });
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email confirmation désinscription: ' . $e->getMessage());
        }
    }
} 