<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AstucesSoumise;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AstuceController extends Controller
{
    /**
     * Affichage du formulaire de soumission d'astuce
     */
    public function create()
    {
        // Catégories d'astuces disponibles
        $categories = [
            'cuisine' => __('Cuisine et alimentation'),
            'menage' => __('Ménage et nettoyage'),
            'bricolage' => __('Bricolage et réparation'),
            'beaute' => __('Beauté et bien-être'),
            'organisation' => __('Organisation et rangement'),
            'jardinage' => __('Jardinage'),
            'economie' => __('Économies et budget'),
            'technologie' => __('Technologie'),
            'sante' => __('Santé'),
            'autre' => __('Autre')
        ];

        // Statistiques pour encourager la participation
        $stats = [
            'total_soumissions' => AstucesSoumise::count(),
            'approuvees' => AstucesSoumise::approuve()->count(),
            'en_attente' => AstucesSoumise::enAttente()->count()
        ];

        // Dernières astuces approuvées (pour inspiration)
        $dernieresApprouvees = AstucesSoumise::approuve()
            ->recent()
            ->limit(3)
            ->get(['titre', 'description', 'categorie']);

        return view('pages.astuces.create', compact(
            'categories',
            'stats',
            'dernieresApprouvees'
        ));
    }

    /**
     * Traitement de la soumission d'astuce
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|min:5|max:200',
            'description' => 'required|string|min:20|max:2000',
            'categorie' => 'required|string|in:cuisine,menage,bricolage,beaute,organisation,jardinage,economie,technologie,sante,autre',
            'nom_soumetteur' => 'required|string|min:2|max:100',
            'email_soumetteur' => 'required|email|max:255',
            'telephone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20',
            'agree_terms' => 'required|accepted',
            'agree_newsletter' => 'boolean',
            'fichiers.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt',
        ], [
            'titre.required' => __('Le titre est obligatoire'),
            'titre.min' => __('Le titre doit contenir au moins 5 caractères'),
            'titre.max' => __('Le titre ne peut pas dépasser 200 caractères'),
            'description.required' => __('La description est obligatoire'),
            'description.min' => __('La description doit contenir au moins 20 caractères'),
            'description.max' => __('La description ne peut pas dépasser 2000 caractères'),
            'categorie.required' => __('Veuillez sélectionner une catégorie'),
            'categorie.in' => __('Catégorie invalide'),
            'nom_soumetteur.required' => __('Votre nom est obligatoire'),
            'email_soumetteur.required' => __('Votre email est obligatoire'),
            'email_soumetteur.email' => __('Format d\'email invalide'),
            'agree_terms.required' => __('Vous devez accepter les conditions d\'utilisation'),
            'fichiers.*.max' => __('Chaque fichier ne peut pas dépasser 10Mo'),
            'fichiers.*.mimes' => __('Types de fichiers autorisés: JPG, PNG, GIF, PDF, DOC, DOCX, TXT'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('Veuillez corriger les erreurs dans le formulaire.'));
        }

        try {
            // Protection anti-spam simple
            if ($this->isSpam($request)) {
                return redirect()->back()
                    ->with('error', __('Votre soumission a été détectée comme spam. Veuillez réessayer.'))
                    ->withInput();
            }

            // Création de l'astuce
            $astuce = AstucesSoumise::create([
                'titre_astuce' => $request->titre,
                'description' => $request->description,
                'nom' => $request->nom_soumetteur,
                'email' => $request->email_soumetteur,
                'status' => AstucesSoumise::STATUS_EN_ATTENTE,
            ]);

            // Gestion des fichiers joints
            if ($request->hasFile('fichiers')) {
                $this->handleFileUploads($astuce, $request->file('fichiers'));
            }

            // Envoi de l'email de confirmation
            $this->sendConfirmationEmail($astuce);

            // Notification aux administrateurs (optionnel)
            $this->notifyAdministrators($astuce);

            return redirect()->route('astuces.success')
                ->with('success', __('Votre astuce a été soumise avec succès ! Vous recevrez un email de confirmation.'))
                ->with('astuce_id', $astuce->id);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la soumission d\'astuce: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue lors de la soumission. Veuillez réessayer.'))
                ->withInput();
        }
    }

    /**
     * Page de confirmation après soumission
     */
    public function success(Request $request)
    {
        if (!session()->has('success') || !session()->has('astuce_id')) {
            return redirect()->route('astuces.create');
        }

        $astuceId = session('astuce_id');
        
        // Informations sur le processus de validation
        $processInfo = [
            'delai_moyen' => '2-5 jours ouvrés',
            'criteres' => [
                __('Originalité et utilité de l\'astuce'),
                __('Clarté de la description'),
                __('Respect des règles communautaires'),
                __('Faisabilité et sécurité')
            ]
        ];

        // Suggestions d'autres actions
        $suggestions = [
            'newsletter' => route('newsletter.subscribe'),
                            'episodes' => \App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'),
                            'blog' => \App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index')
        ];

        return view('pages.astuces.success', compact(
            'astuceId',
            'processInfo',
            'suggestions'
        ));
    }

    /**
     * Suivi du statut d'une astuce soumise
     */
    public function track(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $astuce = AstucesSoumise::where('id', $id)
                ->where('email_soumetteur', $request->email)
                ->firstOrFail();

            $timeline = [
                [
                    'date' => $astuce->date_soumission,
                    'status' => 'soumise',
                    'title' => __('Astuce soumise'),
                    'description' => __('Votre astuce a été reçue et est en attente de validation.')
                ]
            ];

            if ($astuce->date_validation) {
                $timeline[] = [
                    'date' => $astuce->date_validation,
                                'status' => $astuce->status,
            'title' => $astuce->status === AstucesSoumise::STATUS_APPROUVE 
                        ? __('Astuce approuvée') 
                        : __('Astuce rejetée'),
                    'description' => $astuce->commentaire_admin ?: 
                        ($astuce->status === AstucesSoumise::STATUS_APPROUVE 
                            ? __('Félicitations ! Votre astuce a été approuvée.')
                            : __('Votre astuce n\'a pas été retenue cette fois.'))
                ];
            }

            return view('pages.astuces.track', compact('astuce', 'timeline'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', __('Astuce introuvable ou email incorrect.'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors du suivi d\'astuce: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', __('Une erreur est survenue lors de la recherche.'));
        }
    }

    /**
     * Liste des astuces approuvées (publique)
     */
    public function index(Request $request)
    {
        $perPage = 12;
        $category = $request->get('category', 'all');
        $search = $request->get('search');

        try {
            $query = AstucesSoumise::approuve();

            // Filtre par catégorie
            if ($category !== 'all') {
                $query->where('categorie', $category);
            }

            // Recherche textuelle
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('titre_astuce', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $astuces = $query->recent()->paginate($perPage)->withQueryString();

            // Catégories avec compteurs (simplified for now)
            $categories = [
                'cuisine' => 0,
                'menage' => 0,
                'bricolage' => 0,
                'beaute' => 0,
                'organisation' => 0,
                'jardinage' => 0,
                'economie' => 0,
                'technologie' => 0,
                'sante' => 0,
                'autre' => 0
            ];

            // Astuces populaires (les plus récentes pour l'instant)
            $astucesPopulaires = AstucesSoumise::approuve()
                ->recent()
                ->limit(5)
                ->get(['id', 'titre_astuce', 'status']);

            return view('pages.astuces.index', compact(
                'astuces',
                'categories',
                'astucesPopulaires',
                'category',
                'search'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage des astuces: ' . $e->getMessage());
            
            // Create an empty paginated result
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), // items
                0, // total
                $perPage, // per page
                1, // current page
                ['path' => request()->url(), 'pageName' => 'page']
            );
            
            return view('pages.astuces.index', [
                'astuces' => $emptyPaginator,
                'categories' => [],
                'astucesPopulaires' => collect(),
                'category' => 'all',
                'search' => ''
            ]);
        }
    }

    /**
     * Affichage détaillé d'une astuce approuvée
     */
    public function show($id)
    {
        try {
            $astuce = AstucesSoumise::where('id', $id)
                ->approuve()
                ->firstOrFail();

            // Astuces similaires (simplified for now)
            $astucesSimilaires = AstucesSoumise::approuve()
                ->where('id', '!=', $astuce->id)
                ->recent()
                ->limit(4)
                ->get();

            // Meta données pour le SEO
            $seoData = [
                'title' => $astuce->titre_astuce . ' - Astuces L\'Astuce',
                'description' => \Str::limit(strip_tags($astuce->description), 160),
                'url' => route('astuces.show', $astuce->id),
                'type' => 'article'
            ];

            return view('pages.astuces.show', compact(
                'astuce',
                'astucesSimilaires',
                'seoData'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, __('Astuce introuvable'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage de l\'astuce: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('astuces.index'))
                ->with('error', __('Une erreur est survenue lors du chargement de l\'astuce.'));
        }
    }

    /**
     * Gestion des fichiers uploadés (simplified)
     */
    private function handleFileUploads(AstucesSoumise $astuce, array $files)
    {
        // For now, just store the first file path in the fichier_joint field
        if (!empty($files)) {
            try {
                $file = $files[0];
                $path = $file->store('astuces-attachments', 'public');
                $astuce->update(['fichier_joint' => $path]);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'upload de fichier: ' . $e->getMessage());
            }
        }
    }

    /**
     * Envoi de l'email de confirmation
     */
    private function sendConfirmationEmail(AstucesSoumise $astuce)
    {
        try {
            Mail::send('emails.astuce-confirmation', compact('astuce'), function ($message) use ($astuce) {
                $message->to($astuce->email_soumetteur, $astuce->nom_soumetteur)
                        ->subject(__('Confirmation de réception de votre astuce - L\'Astuce'));
            });
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Notification aux administrateurs
     */
    private function notifyAdministrators(AstucesSoumise $astuce)
    {
        try {
            $adminEmails = ['admin@lastuce.com']; // À configurer
            
            foreach ($adminEmails as $email) {
                Mail::send('emails.admin-nouvelle-astuce', compact('astuce'), function ($message) use ($email, $astuce) {
                    $message->to($email)
                            ->subject(__('Nouvelle astuce soumise - L\'Astuce'));
                });
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la notification admin: ' . $e->getMessage());
        }
    }

    /**
     * Détection anti-spam basique
     */
    private function isSpam(Request $request): bool
    {
        // Vérifier si trop de soumissions depuis la même IP
        $recentSubmissions = AstucesSoumise::where('ip_soumetteur', $request->ip())
            ->where('date_soumission', '>=', now()->subHour())
            ->count();

        if ($recentSubmissions >= 3) {
            return true;
        }

        // Vérifier les mots-clés suspects
        $spamKeywords = ['viagra', 'casino', 'poker', 'loan', 'sex'];
        $content = strtolower($request->titre . ' ' . $request->description);
        
        foreach ($spamKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }

        // Vérifier si le titre et la description sont identiques (suspect)
        if (trim(strtolower($request->titre)) === trim(strtolower($request->description))) {
            return true;
        }

        return false;
    }
} 