<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    /**
     * Affichage du formulaire de contact
     */
    public function create()
    {
        // Sujets prédéfinis pour faciliter le traitement
        $sujets = [
            'general' => __('Question générale'),
            'technique' => __('Problème technique'),
            'partenariat' => __('Proposition de partenariat'),
            'presse' => __('Demande presse/média'),
            'astuce' => __('Soumission d\'astuce'),
            'feedback' => __('Commentaire/Suggestion'),
            'autre' => __('Autre sujet')
        ];

        // Informations de contact
        $contactInfo = [
            'email_general' => 'contact@lastuce.com',
            'email_presse' => 'presse@lastuce.com',
            'email_commercial' => 'commercial@lastuce.com',
            'telephone' => '+33 1 23 45 67 89',
            'adresse' => [
                'rue' => '123 Rue de l\'Innovation',
                'ville' => 'Paris',
                'code_postal' => '75001',
                'pays' => 'France'
            ],
            'horaires' => [
                'lundi_vendredi' => '9h00 - 18h00',
                'weekend' => 'Fermé'
            ]
        ];

        // FAQ pour réduire les demandes
        $faq = [
            [
                'question' => __('Comment soumettre une astuce ?'),
                'reponse' => __('Utilisez notre formulaire dédié en cliquant sur "Soumettre une astuce" dans le menu principal.'),
                'link' => route('astuces.create', ['locale' => app()->getLocale()])
            ],
            [
                'question' => __('Puis-je utiliser vos contenus ?'),
                'reponse' => __('Nos contenus sont protégés. Contactez-nous pour toute utilisation commerciale.'),
                'link' => null
            ],
            [
                'question' => __('Comment devenir partenaire ?'),
                'reponse' => __('Consultez notre page dédiée aux partenariats pour plus d\'informations.'),
                'link' => route('partenariats.info', ['locale' => app()->getLocale()])
            ]
        ];

        // Temps de réponse moyen
        $responseTime = [
            'general' => '24-48h',
            'technique' => '2-4h',
            'commercial' => '24h',
            'urgent' => '2h'
        ];

        return view('pages.contact.create', compact(
            'sujets',
            'contactInfo',
            'faq',
            'responseTime'
        ));
    }

    /**
     * Traitement du formulaire de contact
     */
    public function store(Request $request)
    {
        // Rate limiting : 5 messages par heure par IP
        $key = 'contact-form:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->with('error', __('Trop de messages envoyés. Réessayez dans :time minutes.', ['time' => ceil($seconds / 60)]))
                ->withInput();
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|min:2|max:100',
            'prenom' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20',
            'entreprise' => 'nullable|string|max:200',
            'sujet' => 'required|string|in:' . implode(',', array_keys($this->getSujets())),
            'message' => 'required|string|min:10|max:3000',
            'urgence' => 'required|in:faible,normale,elevee,urgente',
            'prefere_reponse' => 'required|in:email,telephone,indifferent',
            'agree_terms' => 'required|accepted',
            'g-recaptcha-response' => 'nullable|string',
            'fichier' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt'
        ], [
            'nom.required' => __('Le nom est obligatoire'),
            'nom.min' => __('Le nom doit contenir au moins 2 caractères'),
            'prenom.required' => __('Le prénom est obligatoire'),
            'email.required' => __('L\'adresse email est obligatoire'),
            'email.email' => __('Format d\'email invalide'),
            'sujet.required' => __('Veuillez sélectionner un sujet'),
            'sujet.in' => __('Sujet invalide'),
            'message.required' => __('Le message est obligatoire'),
            'message.min' => __('Le message doit contenir au moins 10 caractères'),
            'message.max' => __('Le message ne peut pas dépasser 3000 caractères'),
            'urgence.required' => __('Veuillez indiquer le niveau d\'urgence'),
            'urgence.in' => __('Niveau d\'urgence invalide'),
            'prefere_reponse.required' => __('Veuillez indiquer votre préférence de réponse'),
            'agree_terms.required' => __('Vous devez accepter nos conditions d\'utilisation'),
            'fichier.max' => __('Le fichier ne peut pas dépasser 10Mo'),
            'fichier.mimes' => __('Types de fichiers autorisés: JPG, PNG, GIF, PDF, DOC, DOCX, TXT')
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('Veuillez corriger les erreurs dans le formulaire.'));
        }

        try {
            // Protection anti-spam
            if ($this->isSpam($request)) {
                RateLimiter::hit($key); // Compter comme une tentative
                return redirect()->back()
                    ->with('error', __('Message détecté comme spam. Veuillez réessayer.'))
                    ->withInput();
            }

            // Vérification reCAPTCHA
            if ($request->filled('g-recaptcha-response')) {
                if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                    return redirect()->back()
                        ->with('error', __('Vérification reCAPTCHA échouée. Veuillez réessayer.'))
                        ->withInput();
                }
            }

            // Données du message
            $messageData = [
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'entreprise' => $request->entreprise,
                'sujet' => $request->sujet,
                'message' => $request->message,
                'urgence' => $request->urgence,
                'prefere_reponse' => $request->prefere_reponse,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'date_envoi' => now(),
                'reference' => $this->generateReference()
            ];

            // Gestion du fichier joint
            $attachmentPath = null;
            if ($request->hasFile('fichier')) {
                $attachmentPath = $request->file('fichier')->store('contact-attachments', 'private');
                $messageData['fichier_joint'] = $attachmentPath;
            }

            // Déterminer l'email de destination
            $destinationEmail = $this->getDestinationEmail($request->sujet);

            // Envoi de l'email à l'équipe
            $this->sendToTeam($messageData, $destinationEmail, $attachmentPath);

            // Envoi de l'accusé de réception
            $this->sendAcknowledgment($messageData);

            // Enregistrer dans les logs pour suivi
            \Log::info('Message de contact reçu', [
                'reference' => $messageData['reference'],
                'sujet' => $request->sujet,
                'email' => $request->email,
                'urgence' => $request->urgence
            ]);

            // Incrémenter le rate limiting
            RateLimiter::hit($key);

            return redirect()->route('contact.success')
                ->with('success', __('Votre message a été envoyé avec succès !'))
                ->with('reference', $messageData['reference'])
                ->with('response_time', $this->getExpectedResponseTime($request->sujet, $request->urgence));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi du message de contact: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue lors de l\'envoi. Veuillez réessayer.'))
                ->withInput();
        }
    }

    /**
     * Page de confirmation d'envoi
     */
    public function success()
    {
        if (!session()->has('success') || !session()->has('reference')) {
            return redirect()->route('contact.create', ['locale' => app()->getLocale()]);
        }

        $reference = session('reference');
        $responseTime = session('response_time', '24-48h');

        // Prochaines étapes
        $nextSteps = [
            [
                'step' => 1,
                'title' => __('Accusé de réception'),
                'description' => __('Vous devriez recevoir un email de confirmation dans les prochaines minutes.'),
                'status' => 'completed'
            ],
            [
                'step' => 2,
                'title' => __('Traitement'),
                'description' => __('Notre équipe va analyser votre demande.'),
                'status' => 'current'
            ],
            [
                'step' => 3,
                'title' => __('Réponse'),
                'description' => __('Vous recevrez une réponse dans un délai de :time.', ['time' => $responseTime]),
                'status' => 'upcoming'
            ]
        ];

        // Actions suggérées en attendant
        $suggestions = [
            [
                'title' => __('Consultez notre FAQ'),
                'description' => __('Peut-être que votre question y trouve déjà une réponse'),
                'link' => route('faq'),
                'icon' => 'help'
            ],
            [
                'title' => __('Découvrez nos épisodes'),
                'description' => __('En attendant notre réponse, explorez notre contenu'),
                'link' => \App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'),
                'icon' => 'play'
            ],
            [
                'title' => __('Suivez-nous'),
                'description' => __('Restez informé via nos réseaux sociaux'),
                'link' => '#social',
                'icon' => 'share'
            ]
        ];

        return view('pages.contact.success', compact(
            'reference',
            'responseTime',
            'nextSteps',
            'suggestions'
        ));
    }

    /**
     * API pour validation en temps réel du formulaire
     */
    public function validateForm(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'nom' => 'required|string|min:2|max:100',
            'message' => 'required|string|min:10|max:3000'
        ];

        $field = $request->input('field');
        $value = $request->input('value');

        if (!isset($rules[$field])) {
            return response()->json(['valid' => false, 'message' => 'Champ invalide']);
        }

        $validator = Validator::make([$field => $value], [$field => $rules[$field]]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => $validator->errors()->first($field)
            ]);
        }

        return response()->json(['valid' => true]);
    }

    /**
     * Méthodes privées
     */

    private function getSujets(): array
    {
        return [
            'general' => __('Question générale'),
            'technique' => __('Problème technique'),
            'partenariat' => __('Proposition de partenariat'),
            'presse' => __('Demande presse/média'),
            'astuce' => __('Soumission d\'astuce'),
            'feedback' => __('Commentaire/Suggestion'),
            'autre' => __('Autre sujet')
        ];
    }

    private function generateReference(): string
    {
        return 'CONT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    private function getDestinationEmail(string $sujet): string
    {
        return match($sujet) {
            'technique' => 'technique@lastuce.com',
            'partenariat' => 'commercial@lastuce.com',
            'presse' => 'presse@lastuce.com',
            'astuce' => 'astuces@lastuce.com',
            default => 'contact@lastuce.com'
        };
    }

    private function getExpectedResponseTime(string $sujet, string $urgence): string
    {
        if ($urgence === 'urgente') {
            return '2-4h';
        }

        return match($sujet) {
            'technique' => '4-8h',
            'partenariat' => '24-48h',
            'presse' => '12-24h',
            default => '24-48h'
        };
    }

    private function isSpam(Request $request): bool
    {
        $content = strtolower($request->nom . ' ' . $request->message . ' ' . $request->email);
        
        // Mots-clés suspects
        $spamKeywords = [
            'viagra', 'casino', 'poker', 'loan', 'bitcoin', 'crypto', 
            'pharmacy', 'pills', 'weight loss', 'sex', 'porn'
        ];
        
        foreach ($spamKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }

        // Vérifier les liens suspects
        if (preg_match_all('/https?:\/\/[^\s]+/', $request->message, $matches)) {
            if (count($matches[0]) > 2) { // Plus de 2 liens = suspect
                return true;
            }
        }

        // Vérifier si nom et prénom sont identiques (suspect)
        if (strtolower($request->nom) === strtolower($request->prenom)) {
            return true;
        }

        // Message trop court ou trop répétitif
        $words = explode(' ', $request->message);
        $uniqueWords = array_unique($words);
        if (count($words) > 10 && count($uniqueWords) / count($words) < 0.3) {
            return true; // Moins de 30% de mots uniques
        }

        return false;
    }

    private function verifyRecaptcha(string $response): bool
    {
        $secretKey = config('services.recaptcha.secret');
        
        if (!$secretKey) {
            return true;
        }

        try {
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => request()->ip()
            ]);

            $data = $response->json();
            return $data['success'] ?? false;

        } catch (\Exception $e) {
            \Log::error('Erreur vérification reCAPTCHA contact: ' . $e->getMessage());
            return false;
        }
    }

    private function sendToTeam(array $messageData, string $destinationEmail, ?string $attachmentPath): void
    {
        try {
            Mail::send('emails.contact-equipe', compact('messageData'), function ($message) use ($destinationEmail, $messageData, $attachmentPath) {
                $message->to($destinationEmail)
                        ->replyTo($messageData['email'], $messageData['prenom'] . ' ' . $messageData['nom'])
                        ->subject('[' . $messageData['reference'] . '] Nouveau message - ' . $this->getSujets()[$messageData['sujet']]);
                
                if ($attachmentPath) {
                    $message->attach(storage_path('app/private/' . $attachmentPath));
                }
            });

            // Copie pour l'équipe générale si sujet spécialisé
            if ($destinationEmail !== 'contact@lastuce.com') {
                Mail::send('emails.contact-equipe', compact('messageData'), function ($message) use ($messageData) {
                    $message->to('contact@lastuce.com')
                            ->subject('[COPIE] [' . $messageData['reference'] . '] Nouveau message - ' . $this->getSujets()[$messageData['sujet']]);
                });
            }

        } catch (\Exception $e) {
            \Log::error('Erreur envoi email équipe contact: ' . $e->getMessage());
        }
    }

    private function sendAcknowledgment(array $messageData): void
    {
        try {
            Mail::send('emails.contact-accuse-reception', compact('messageData'), function ($message) use ($messageData) {
                $message->to($messageData['email'], $messageData['prenom'] . ' ' . $messageData['nom'])
                        ->subject(__('Accusé de réception - Votre message à L\'Astuce [' . $messageData['reference'] . ']'));
            });

        } catch (\Exception $e) {
            \Log::error('Erreur envoi accusé réception contact: ' . $e->getMessage());
        }
    }
} 