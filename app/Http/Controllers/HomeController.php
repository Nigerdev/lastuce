<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Episode;
use App\Models\BlogArticle;
use App\Models\NewsletterAbonne;
use App\Models\AstucesSoumise;
use App\Models\Partenariat;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Affichage de la page d'accueil
     */
    public function index()
    {
        try {
            // Récupérer les derniers épisodes publiés
            $latestEpisodes = Episode::published()
                ->recent()
                ->limit(6)
                ->get();

            // Récupérer les derniers articles de blog (actualités coulisses)
            $latestBlogArticles = BlogArticle::publishedAndVisible()
                ->recent()
                ->limit(3)
                ->get();

            // Statistiques pour la page d'accueil
            $stats = [
                'total_episodes' => Episode::published()->count(),
                'newsletter_subscribers' => NewsletterAbonne::actif()->count(),
                'approved_astuces' => AstucesSoumise::approuve()->count(),
            ];

            // Épisode vedette (le plus récent)
            $featuredEpisode = Episode::published()
                ->whereNotNull('youtube_url')
                ->recent()
                ->first();

            // Témoignages d'utilisateurs (données statiques pour l'exemple)
            $testimonials = $this->getTestimonials();

            // Récupérer les partenaires acceptés
            $partners = Partenariat::accepte()
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

            return view('pages.home', compact(
                'latestEpisodes',
                'latestBlogArticles',
                'stats',
                'featuredEpisode',
                'testimonials',
                'partners'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur sur la page d\'accueil: ' . $e->getMessage());
            
            // En cas d'erreur, afficher une page d'accueil minimale
            return view('pages.home', [
                'latestEpisodes' => collect(),
                'latestBlogArticles' => collect(),
                'stats' => ['total_episodes' => 0, 'newsletter_subscribers' => 0, 'approved_astuces' => 0],
                'featuredEpisode' => null,
                'testimonials' => [],
                'partners' => collect()
            ]);
        }
    }

    /**
     * Page À propos
     */
    public function about()
    {
        // Informations sur l'émission
        $showInfo = [
            'founded_year' => '2019',
            'total_episodes' => Episode::published()->count(),
            'team_members' => 5,
            'countries_reached' => 15
        ];

        // Membres de l'équipe (données statiques)
        $teamMembers = [
            [
                'name' => 'Marie Dubois',
                'role' => __('Animatrice principale'),
                'bio' => __('Passionnée d\'astuces du quotidien depuis 10 ans'),
                'photo' => 'team/marie.jpg'
            ],
            [
                'name' => 'Pierre Martin',
                'role' => __('Producteur'),
                'bio' => __('Expert en production audiovisuelle'),
                'photo' => 'team/pierre.jpg'
            ],
            [
                'name' => 'Sophie Lefebvre',
                'role' => __('Rédactrice en chef'),
                'bio' => __('Spécialiste en lifestyle et bien-être'),
                'photo' => 'team/sophie.jpg'
            ]
        ];

        return view('pages.about', compact('showInfo', 'teamMembers'));
    }

    /**
     * Changer la langue de l'interface
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changeLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|in:fr,en'
        ]);

        $locale = $request->input('locale');
        
        // Vérifier que la locale est supportée
        if (!array_key_exists($locale, config('app.supported_locales', []))) {
            return response()->json(['error' => 'Langue non supportée'], 400);
        }

        // Sauvegarder en session
        session(['locale' => $locale]);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => __('app.success.language_changed', [], $locale)
        ]);
    }

    /**
     * Page de recherche globale
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $query = $request->input('q');
        $results = collect();

        try {
            // Rechercher dans les épisodes
            $episodes = Episode::search($query)
                ->published()
                ->limit(10)
                ->get()
                ->map(function ($episode) {
                    return [
                        'type' => 'episode',
                        'title' => $episode->titre,
                        'excerpt' => $episode->description,
                        'url' => route('episodes.show', $episode->slug),
                        'date' => $episode->date_diffusion,
                        'meta' => $episode->formatted_type
                    ];
                });

            // Rechercher dans les articles de blog
            $articles = BlogArticle::search($query)
                ->publishedAndVisible()
                ->limit(10)
                ->get()
                ->map(function ($article) {
                    return [
                        'type' => 'article',
                        'title' => $article->titre,
                        'excerpt' => $article->extrait,
                        'url' => route('blog.show', $article->slug),
                        'date' => $article->date_publication,
                        'meta' => __('Article de blog')
                    ];
                });

            $results = $episodes->concat($articles)->sortByDesc('date');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la recherche: ' . $e->getMessage());
            $results = collect();
        }

        return view('pages.search', compact('query', 'results'));
    }

    /**
     * API endpoint pour l'autocomplétion de recherche
     */
    public function searchSuggestions(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:50'
        ]);

        $query = $request->input('q');
        $suggestions = [];

        try {
            // Suggestions d'épisodes
            $episodeSuggestions = Episode::where('titre', 'like', "%{$query}%")
                ->published()
                ->limit(5)
                ->pluck('titre')
                ->toArray();

            // Suggestions d'articles
            $articleSuggestions = BlogArticle::where('titre', 'like', "%{$query}%")
                ->publishedAndVisible()
                ->limit(5)
                ->pluck('titre')
                ->toArray();

            $suggestions = array_merge($episodeSuggestions, $articleSuggestions);
            $suggestions = array_unique($suggestions);
            $suggestions = array_slice($suggestions, 0, 8);

        } catch (\Exception $e) {
            \Log::error('Erreur suggestions de recherche: ' . $e->getMessage());
        }

        return response()->json($suggestions);
    }

    /**
     * Page de maintenance programmée
     */
    public function maintenance()
    {
        return view('pages.maintenance');
    }

    /**
     * Témoignages d'utilisateurs (données statiques)
     */
    private function getTestimonials()
    {
        return [
            [
                'name' => 'Claire Dubois',
                'location' => 'Paris',
                'comment' => __('Cette émission a changé ma façon de voir les petites choses du quotidien. Les astuces sont vraiment pratiques !'),
                'rating' => 5,
                'avatar' => 'avatars/claire.jpg'
            ],
            [
                'name' => 'Marc Leroy',
                'location' => 'Lyon',
                'comment' => __('J\'adore la simplicité des conseils. Mes favoris sont les épisodes sur l\'organisation !'),
                'rating' => 5,
                'avatar' => 'avatars/marc.jpg'
            ],
            [
                'name' => 'Sarah Wilson',
                'location' => 'Montreal',
                'comment' => __('Great show! The tips are universal and work in any culture. Keep it up!'),
                'rating' => 5,
                'avatar' => 'avatars/sarah.jpg'
            ]
        ];
    }

    /**
     * Sitemap XML pour le SEO
     */
    public function sitemap()
    {
        $episodes = Episode::published()->recent()->get();
        $articles = BlogArticle::publishedAndVisible()->recent()->get();

        return response()->view('sitemap', compact('episodes', 'articles'))
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Flux RSS des derniers contenus
     */
    public function rss()
    {
        $episodes = Episode::published()->recent()->limit(20)->get();
        $articles = BlogArticle::publishedAndVisible()->recent()->limit(10)->get();

        return response()->view('rss', compact('episodes', 'articles'))
            ->header('Content-Type', 'application/rss+xml');
    }

    /**
     * Page de statistiques publiques
     */
    public function stats()
    {
        $stats = [
            'episodes' => [
                'total' => Episode::published()->count(),
                'ce_mois' => Episode::published()->whereMonth('date_diffusion', now()->month)->count(),
                'par_type' => [
                    'episode' => Episode::published()->episodes()->count(),
                    'coulisse' => Episode::published()->coulisses()->count(),
                    'bonus' => Episode::published()->bonus()->count(),
                ]
            ],
            'newsletter' => NewsletterAbonne::getStatistiques(),
            'astuces' => AstucesSoumise::countByStatus(),
            'blog' => [
                'total' => BlogArticle::published()->count(),
                'ce_mois' => BlogArticle::published()->whereMonth('date_publication', now()->month)->count()
            ]
        ];

        return view('pages.stats', compact('stats'));
    }
} 