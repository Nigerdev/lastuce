<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partenariat;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PartenaritController extends Controller
{
    /**
     * Affichage du formulaire de demande de partenariat
     */
    public function create()
    {
        // Types de partenariat disponibles
        $typesPartenariat = [
            'sponsoring' => __('Sponsoring d\'épisode'),
            'collaboration' => __('Collaboration de contenu'),
            'produit' => __('Placement de produit'),
            'evenement' => __('Partenariat événementiel'),
            'affiliation' => __('Programme d\'affiliation'),
            'echange' => __('Échange de services'),
            'autre' => __('Autre proposition')
        ];

        // Secteurs d'activité
        $secteurs = [
            'technologie' => __('Technologie'),
            'lifestyle' => __('Lifestyle et bien-être'),
            'maison' => __('Maison et décoration'),
            'cuisine' => __('Cuisine et alimentation'),
            'beaute' => __('Beauté et cosmétiques'),
            'mode' => __('Mode et accessoires'),
            'sport' => __('Sport et fitness'),
            'education' => __('Éducation et formation'),
            'voyage' => __('Voyage et tourisme'),
            'finance' => __('Finance et assurance'),
            'sante' => __('Santé et médical'),
            'autre' => __('Autre secteur')
        ];

        // Budgets de partenariat
        $budgets = [
            'moins_1000' => __('Moins de 1 000€'),
            '1000_5000' => __('1 000€ - 5 000€'),
            '5000_10000' => __('5 000€ - 10 000€'),
            '10000_25000' => __('10 000€ - 25 000€'),
            'plus_25000' => __('Plus de 25 000€'),
            'negociable' => __('À négocier'),
            'echange' => __('Échange de services')
        ];

        // Statistiques pour rassurer
        $stats = [
            'partenaires_actifs' => 15, // Données statiques pour l'exemple
            'projets_realises' => Partenariat::accepte()->count(),
            'satisfaction' => '98%'
        ];

        // Témoignages de partenaires
        $testimonials = [
            [
                'name' => 'Marie Durand',
                'company' => 'Green Home',
                'message' => __('Un partenariat très enrichissant avec une équipe professionnelle.'),
                'rating' => 5
            ],
            [
                'name' => 'Pierre Dubois',
                'company' => 'Tech Solutions',
                'message' => __('Excellent retour sur investissement et visibilité optimale.'),
                'rating' => 5
            ]
        ];

        return view('pages.partenariats.create', compact(
            'typesPartenariat',
            'secteurs',
            'budgets',
            'stats',
            'testimonials'
        ));
    }

    /**
     * Traitement de la demande de partenariat
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_entreprise' => 'required|string|min:2|max:200',
            'secteur_activite' => 'required|string|in:' . implode(',', array_keys($this->getSecteurs())),
            'site_web' => 'nullable|url|max:255',
            'nom_contact' => 'required|string|min:2|max:100',
            'email_contact' => 'required|email|max:255',
            'telephone_contact' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20',
            'poste_contact' => 'required|string|max:100',
            'type_partenariat' => 'required|string|in:' . implode(',', array_keys($this->getTypesPartenariat())),
            'description_projet' => 'required|string|min:50|max:3000',
            'objectifs' => 'required|string|min:20|max:1000',
            'budget_envisage' => 'required|string|in:' . implode(',', array_keys($this->getBudgets())),
            'duree_souhaitee' => 'nullable|string|max:100',
            'date_souhaitee' => 'nullable|date|after:today',
            'audience_cible' => 'nullable|string|max:500',
            'experience_partenariats' => 'nullable|string|max:1000',
            'fichier_presentation' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx',
            'agree_terms' => 'required|accepted',
            'agree_contact' => 'required|accepted'
        ], [
            'nom_entreprise.required' => __('Le nom de l\'entreprise est obligatoire'),
            'secteur_activite.required' => __('Veuillez sélectionner un secteur d\'activité'),
            'nom_contact.required' => __('Le nom du contact est obligatoire'),
            'email_contact.required' => __('L\'email de contact est obligatoire'),
            'email_contact.email' => __('Format d\'email invalide'),
            'poste_contact.required' => __('Le poste du contact est obligatoire'),
            'type_partenariat.required' => __('Veuillez sélectionner un type de partenariat'),
            'description_projet.required' => __('La description du projet est obligatoire'),
            'description_projet.min' => __('La description doit contenir au moins 50 caractères'),
            'objectifs.required' => __('Les objectifs sont obligatoires'),
            'budget_envisage.required' => __('Veuillez indiquer le budget envisagé'),
            'date_souhaitee.after' => __('La date souhaitée doit être dans le futur'),
            'fichier_presentation.max' => __('Le fichier ne peut pas dépasser 10Mo'),
            'fichier_presentation.mimes' => __('Types de fichiers autorisés: PDF, DOC, DOCX, PPT, PPTX'),
            'agree_terms.required' => __('Vous devez accepter les conditions générales'),
            'agree_contact.required' => __('Vous devez accepter d\'être contacté')
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
                return redirect()->back()
                    ->with('error', __('Votre demande a été détectée comme spam. Veuillez réessayer.'))
                    ->withInput();
            }

            // Création de la demande de partenariat
            $partenariat = Partenariat::create([
                'nom_entreprise' => $request->nom_entreprise,
                'secteur_activite' => $request->secteur_activite,
                'site_web' => $request->site_web,
                'nom_contact' => $request->nom_contact,
                'email_contact' => $request->email_contact,
                'telephone_contact' => $request->telephone_contact,
                'poste_contact' => $request->poste_contact,
                'type_partenariat' => $request->type_partenariat,
                'description_projet' => $request->description_projet,
                'objectifs' => $request->objectifs,
                'budget_envisage' => $request->budget_envisage,
                'duree_souhaitee' => $request->duree_souhaitee,
                'date_souhaitee' => $request->date_souhaitee,
                'audience_cible' => $request->audience_cible,
                'experience_partenariats' => $request->experience_partenariats,
                'status' => Partenariat::STATUS_NOUVEAU,
                'ip_demandeur' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'date_demande' => now(),
            ]);

            // Gestion du fichier de présentation
            if ($request->hasFile('fichier_presentation')) {
                $partenariat->addMediaFromRequest('fichier_presentation')
                    ->toMediaCollection('presentations');
            }

            // Envoi de l'email de confirmation
            $this->sendConfirmationEmail($partenariat);

            // Notification à l'équipe commerciale
            $this->notifyBusinessTeam($partenariat);

            return redirect()->route('partenariats.success')
                ->with('success', __('Votre demande de partenariat a été envoyée avec succès !'))
                ->with('partenariat_id', $partenariat->id);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la soumission de partenariat: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue lors de l\'envoi. Veuillez réessayer.'))
                ->withInput();
        }
    }

    /**
     * Page de confirmation après envoi
     */
    public function success(Request $request)
    {
        if (!session()->has('success') || !session()->has('partenariat_id')) {
            return redirect()->route('partenariats.create');
        }

        $partenaritId = session('partenariat_id');
        
        // Processus de traitement
        $processInfo = [
            'delai_reponse' => '3-5 jours ouvrés',
            'etapes' => [
                __('Réception et accusé de réception'),
                __('Analyse de la demande par notre équipe'),
                __('Évaluation de la compatibilité'),
                __('Proposition commerciale ou refus motivé'),
                __('Négociation et finalisation')
            ]
        ];

        // Prochaines étapes suggérées
        $nextSteps = [
            'preparation' => [
                'title' => __('Préparez votre présentation'),
                'description' => __('Rassemblez vos éléments de marque et KPIs')
            ],
            'portfolio' => [
                'title' => __('Consultez nos réalisations'),
                'description' => __('Découvrez nos partenariats précédents'),
                'link' => \App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index')
            ],
            'contact' => [
                'title' => __('Questions urgentes ?'),
                'description' => __('Contactez-nous directement'),
                'link' => route('contact')
            ]
        ];

        return view('pages.partenariats.success', compact(
            'partenaritId',
            'processInfo',
            'nextSteps'
        ));
    }

    /**
     * Suivi d'une demande de partenariat
     */
    public function track(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $partenariat = Partenariat::where('id', $id)
                ->where('email_contact', $request->email)
                ->firstOrFail();

            // Construction de la timeline
            $timeline = [
                [
                    'date' => $partenariat->date_demande,
                    'status' => 'nouveau',
                    'title' => __('Demande reçue'),
                    'description' => __('Votre demande de partenariat a été reçue et enregistrée.')
                ]
            ];

            if ($partenariat->status !== Partenariat::STATUS_NOUVEAU) {
                $timeline[] = [
                    'date' => $partenariat->updated_at,
                                    'status' => $partenariat->status,
                'title' => $this->getStatusTitle($partenariat->status),
                'description' => $this->getStatusDescription($partenariat->status, $partenariat->notes_internes)
                ];
            }

            // Prochaines étapes selon le statut
            $nextActions = $this->getNextActions($partenariat->status);

            return view('pages.partenariats.track', compact(
                'partenariat',
                'timeline',
                'nextActions'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', __('Demande introuvable ou email incorrect.'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors du suivi de partenariat: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue lors de la recherche.'));
        }
    }

    /**
     * Page d'information sur les partenariats
     */
    public function info()
    {
        // Types de partenariats avec détails
        $partnershipTypes = [
            'sponsoring' => [
                'title' => __('Sponsoring d\'épisode'),
                'description' => __('Votre marque mise en avant dans un épisode dédié'),
                'benefits' => [
                    __('Mention de votre marque en début et fin d\'épisode'),
                    __('Intégration naturelle de vos produits'),
                    __('Article dédié sur notre blog'),
                    __('Partage sur nos réseaux sociaux')
                ],
                'audience' => '10K-50K vues par épisode',
                'budget' => 'À partir de 2 000€'
            ],
            'collaboration' => [
                'title' => __('Collaboration de contenu'),
                'description' => __('Co-création de contenu adapté à votre secteur'),
                'benefits' => [
                    __('Contenu original et authentique'),
                    __('Expertise reconnue dans votre domaine'),
                    __('Audience qualifiée et engagée'),
                    __('Réutilisation pour vos propres canaux')
                ],
                'audience' => 'Audience ciblée selon votre secteur',
                'budget' => 'À partir de 1 500€'
            ],
            'affiliation' => [
                'title' => __('Programme d\'affiliation'),
                'description' => __('Recommandations authentiques avec suivi des conversions'),
                'benefits' => [
                    __('Recommandations naturelles et crédibles'),
                    __('Suivi précis des conversions'),
                    __('Rémunération à la performance'),
                    __('Audience qualifiée et réceptive')
                ],
                'audience' => 'Selon les épisodes pertinents',
                'budget' => 'Commission sur ventes'
            ]
        ];

        // Métriques de performance
        $metrics = [
            'monthly_views' => '250K',
            'avg_engagement' => '8.5%',
            'subscriber_growth' => '+15%/mois',
            'demographics' => [
                'age_25_34' => '35%',
                'age_35_44' => '28%',
                'female' => '68%',
                'france' => '75%'
            ]
        ];

        // Partenaires actuels (exemples)
        $currentPartners = [
            ['name' => 'EcoVie', 'logo' => 'partners/ecovie.png', 'sector' => 'Bien-être'],
            ['name' => 'TechHome', 'logo' => 'partners/techhome.png', 'sector' => 'Technologie'],
            ['name' => 'GreenCook', 'logo' => 'partners/greencook.png', 'sector' => 'Cuisine'],
        ];

        return view('pages.partenariats.info', compact(
            'partnershipTypes',
            'metrics',
            'currentPartners'
        ));
    }

    /**
     * Envoi de l'email de confirmation
     */
    private function sendConfirmationEmail(Partenariat $partenariat)
    {
        try {
            Mail::send('emails.partenariat-confirmation', compact('partenariat'), function ($message) use ($partenariat) {
                $message->to($partenariat->email_contact, $partenariat->nom_contact)
                        ->subject(__('Confirmation de réception - Demande de partenariat L\'Astuce'));
            });
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email de confirmation partenariat: ' . $e->getMessage());
        }
    }

    /**
     * Notification à l'équipe commerciale
     */
    private function notifyBusinessTeam(Partenariat $partenariat)
    {
        try {
            $businessEmails = ['commercial@lastuce.com', 'direction@lastuce.com'];
            
            foreach ($businessEmails as $email) {
                Mail::send('emails.admin-nouveau-partenariat', compact('partenariat'), function ($message) use ($email, $partenariat) {
                    $message->to($email)
                            ->subject(__('Nouvelle demande de partenariat - L\'Astuce'));
                });
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la notification équipe commerciale: ' . $e->getMessage());
        }
    }

    /**
     * Détection anti-spam pour les partenariats
     */
    private function isSpam(Request $request): bool
    {
        // Limite par IP
        $recentSubmissions = Partenariat::where('ip_demandeur', $request->ip())
            ->where('date_demande', '>=', now()->subHours(24))
            ->count();

        if ($recentSubmissions >= 2) {
            return true;
        }

        // Mots-clés suspects
        $spamKeywords = ['bitcoin', 'crypto', 'loan', 'casino', 'sex', 'viagra', 'pharmacy'];
        $content = strtolower($request->description_projet . ' ' . $request->objectifs . ' ' . $request->nom_entreprise);
        
        foreach ($spamKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }

        // Email temporaire/suspect
        $suspiciousEmails = ['@guerrillamail.', '@10minutemail.', '@tempmail.'];
        foreach ($suspiciousEmails as $domain) {
            if (strpos($request->email_contact, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtenir le titre d'un statut
     */
    private function getStatusTitle($status): string
    {
        return match($status) {
            Partenariat::STATUS_EN_COURS => __('Demande en cours d\'analyse'),
            Partenariat::STATUS_ACCEPTE => __('Demande acceptée'),
            Partenariat::STATUS_REFUSE => __('Demande refusée'),
            default => __('Statut inconnu')
        };
    }

    /**
     * Obtenir la description d'un statut
     */
    private function getStatusDescription($status, $notes = null): string
    {
        $description = match($status) {
            Partenariat::STATUS_EN_COURS => __('Notre équipe étudie actuellement votre proposition.'),
            Partenariat::STATUS_ACCEPTE => __('Félicitations ! Votre demande a été acceptée.'),
            Partenariat::STATUS_REFUSE => __('Votre demande n\'a pas été retenue cette fois.'),
            default => __('Statut en cours de mise à jour.')
        };

        return $notes ? $description . ' ' . $notes : $description;
    }

    /**
     * Obtenir les prochaines actions selon le statut
     */
    private function getNextActions($status): array
    {
        return match($status) {
            Partenariat::STATUS_NOUVEAU => [
                __('Patientez pendant que nous analysons votre demande'),
                __('Préparez vos éléments de présentation'),
                __('Consultez nos conditions de partenariat')
            ],
            Partenariat::STATUS_EN_COURS => [
                __('Notre équipe vous contactera sous peu'),
                __('Préparez votre dossier commercial'),
                __('Restez disponible pour un échange téléphonique')
            ],
            Partenariat::STATUS_ACCEPTE => [
                __('Attendez notre proposition commerciale'),
                __('Préparez votre brief créatif'),
                __('Planifiez les échéances du projet')
            ],
            Partenariat::STATUS_REFUSE => [
                __('Vous pouvez soumettre une nouvelle demande dans 6 mois'),
                __('Consultez nos autres types de partenariats'),
                __('Suivez notre évolution pour de futures opportunités')
            ],
            default => []
        };
    }

    /**
     * Helper methods pour les options de formulaire
     */
    private function getTypesPartenariat(): array
    {
        return [
            'sponsoring' => __('Sponsoring d\'épisode'),
            'collaboration' => __('Collaboration de contenu'),
            'produit' => __('Placement de produit'),
            'evenement' => __('Partenariat événementiel'),
            'affiliation' => __('Programme d\'affiliation'),
            'echange' => __('Échange de services'),
            'autre' => __('Autre proposition')
        ];
    }

    private function getSecteurs(): array
    {
        return [
            'technologie' => __('Technologie'),
            'lifestyle' => __('Lifestyle et bien-être'),
            'maison' => __('Maison et décoration'),
            'cuisine' => __('Cuisine et alimentation'),
            'beaute' => __('Beauté et cosmétiques'),
            'mode' => __('Mode et accessoires'),
            'sport' => __('Sport et fitness'),
            'education' => __('Éducation et formation'),
            'voyage' => __('Voyage et tourisme'),
            'finance' => __('Finance et assurance'),
            'sante' => __('Santé et médical'),
            'autre' => __('Autre secteur')
        ];
    }

    private function getBudgets(): array
    {
        return [
            'moins_1000' => __('Moins de 1 000€'),
            '1000_5000' => __('1 000€ - 5 000€'),
            '5000_10000' => __('5 000€ - 10 000€'),
            '10000_25000' => __('10 000€ - 25 000€'),
            'plus_25000' => __('Plus de 25 000€'),
            'negociable' => __('À négocier'),
            'echange' => __('Échange de services')
        ];
    }
} 