<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogArticle;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /**
     * Liste des articles de blog avec pagination et filtres
     */
    public function index(Request $request)
    {
        try {
            $perPage = 12;
            $category = $request->get('category', 'all');
            $search = $request->get('search');
            $sortBy = $request->get('sort', 'recent'); // recent, oldest, popular, read_time

            // Construction de la requête
            $query = BlogArticle::publishedAndVisible();

            // Recherche textuelle
            if ($search) {
                $query->search($search);
            }

            // Tri (simplified)
            switch ($sortBy) {
                case 'oldest':
                    $query->orderBy('date_publication', 'asc');
                    break;
                case 'recent':
                default:
                    $query->recent();
                    break;
            }

            $articles = $query->paginate($perPage)->withQueryString();

            // Simplified data for sidebar
            $categories = []; // No categories in current schema
            
            $popularArticles = BlogArticle::publishedAndVisible()
                ->recent()
                ->limit(5)
                ->get(['id', 'titre', 'slug', 'date_publication']);

            $recentArticles = BlogArticle::publishedAndVisible()
                ->recent()
                ->limit(5)
                ->get(['id', 'titre', 'slug', 'date_publication']);

            $popularTags = []; // No tags in current schema

            return view('pages.blog.index', compact(
                'articles',
                'categories',
                'popularArticles',
                'recentArticles',
                'popularTags',
                'category',
                'search',
                'sortBy'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage du blog: ' . $e->getMessage());
            
            return redirect()->route('home', ['locale' => app()->getLocale()])
                ->with('error', __('Une erreur est survenue lors du chargement du blog.'));
        }
    }

    /**
     * Affichage d'un article de blog
     */
    public function show(Request $request, $slug)
    {
        try {
            $article = BlogArticle::where('slug', $slug)
                ->publishedAndVisible()
                ->firstOrFail();

            // Incrémenter le compteur de vues (throttle par IP)
            $cacheKey = 'blog_view_' . $article->id . '_' . $request->ip();
            if (!Cache::has($cacheKey)) {
                $article->increment('vues');
                Cache::put($cacheKey, true, 3600); // 1 heure
            }

            // Articles liés (simplified)
            $relatedArticles = BlogArticle::publishedAndVisible()
                ->where('id', '!=', $article->id)
                ->recent()
                ->limit(4)
                ->get();

            // Article précédent et suivant
            $previousArticle = BlogArticle::publishedAndVisible()
                ->where('date_publication', '<', $article->date_publication)
                ->orderBy('date_publication', 'desc')
                ->first(['id', 'titre', 'slug']);

            $nextArticle = BlogArticle::publishedAndVisible()
                ->where('date_publication', '>', $article->date_publication)
                ->orderBy('date_publication', 'asc')
                ->first(['id', 'titre', 'slug']);

            // Meta données pour le SEO
            $seoData = [
                'title' => $article->titre . ' - Blog L\'Astuce',
                'description' => $article->meta_description ?: $article->extrait,
                'keywords' => [],
                'image' => $article->image,
                'url' => route('blog.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]),
                'type' => 'article',
                'published_time' => $article->date_publication ? $article->date_publication->toISOString() : null,
                'modified_time' => $article->updated_at->toISOString(),
                'author' => 'L\'équipe L\'Astuce',
                'reading_time' => null
            ];

            // Breadcrumb
            $breadcrumb = [
                ['title' => __('Accueil'), 'url' => \App\Helpers\LocalizationHelper::getLocalizedRoute('home')],
                ['title' => __('Blog'), 'url' => \App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index')],
                ['title' => $article->titre, 'url' => null]
            ];

            return view('pages.blog.show', compact(
                'article',
                'relatedArticles',
                'previousArticle',
                'nextArticle',
                'seoData',
                'breadcrumb'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, __('Article introuvable'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage de l\'article: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index'))
                ->with('error', __('Une erreur est survenue lors du chargement de l\'article.'));
        }
    }

    /**
     * Articles par catégorie
     */
    public function category(Request $request, $category)
    {
        try {
            $perPage = 12;
            $sortBy = $request->get('sort', 'recent');

            $query = BlogArticle::publishedAndVisible()
                ->where('categorie', $category)
                ->with('media');

            // Tri
            switch ($sortBy) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'popular':
                    $query->orderBy('vues', 'desc');
                    break;
                case 'recent':
                default:
                    $query->recent();
                    break;
            }

            $articles = $query->paginate($perPage)->withQueryString();

            // Informations sur la catégorie
            $categoryInfo = [
                'name' => $this->getCategoryName($category),
                'description' => $this->getCategoryDescription($category),
                'total_articles' => BlogArticle::publishedAndVisible()->where('categorie', $category)->count()
            ];

            // Articles populaires de cette catégorie
            $popularInCategory = BlogArticle::publishedAndVisible()
                ->where('categorie', $category)
                ->orderBy('vues', 'desc')
                ->limit(5)
                ->get(['id', 'titre', 'slug', 'vues']);

            return view('pages.blog.category', compact(
                'articles',
                'category',
                'categoryInfo',
                'popularInCategory',
                'sortBy'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage de la catégorie blog: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index'))
                ->with('error', __('Une erreur est survenue lors du chargement de la catégorie.'));
        }
    }

    /**
     * Articles par tag
     */
    public function tag(Request $request, $tag)
    {
        try {
            $perPage = 12;
            
            $articles = BlogArticle::publishedAndVisible()
                ->whereJsonContains('mots_cles', $tag)
                ->with('media')
                ->recent()
                ->paginate($perPage);

            $tagInfo = [
                'name' => $tag,
                'total_articles' => BlogArticle::publishedAndVisible()
                    ->whereJsonContains('mots_cles', $tag)
                    ->count()
            ];

            // Tags similaires
            $relatedTags = $this->getRelatedTags($tag);

            return view('pages.blog.tag', compact(
                'articles',
                'tag',
                'tagInfo',
                'relatedTags'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage du tag blog: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index'))
                ->with('error', __('Une erreur est survenue lors du chargement du tag.'));
        }
    }

    /**
     * Archive des articles par année/mois
     */
    public function archive(Request $request, $year = null, $month = null)
    {
        try {
            $query = BlogArticle::publishedAndVisible();

            if ($year) {
                $query->whereYear('date_publication', $year);
                
                if ($month) {
                    $query->whereMonth('date_publication', $month);
                }
            }

            $articles = $query->with('media')
                ->recent()
                ->paginate(12);

            // Données d'archive pour la navigation
            $archiveData = Cache::remember('blog_archive_data', 3600, function () {
                return BlogArticle::publishedAndVisible()
                    ->selectRaw('YEAR(date_publication) as year, MONTH(date_publication) as month, COUNT(*) as count')
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get()
                    ->groupBy('year');
            });

            return view('pages.blog.archive', compact(
                'articles',
                'archiveData',
                'year',
                'month'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage des archives blog: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index'))
                ->with('error', __('Une erreur est survenue lors du chargement des archives.'));
        }
    }

    /**
     * Recherche d'articles
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'category' => 'sometimes|string',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        try {
            $query = BlogArticle::publishedAndVisible()->with('media');
            
            // Recherche textuelle
            $searchTerm = $request->get('q');
            $query->search($searchTerm);

            // Filtres
            if ($request->filled('category')) {
                $query->where('categorie', $request->get('category'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date_publication', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date_publication', '<=', $request->get('date_to'));
            }

            $articles = $query->recent()->paginate(12)->withQueryString();

            // Suggestions si peu de résultats
            $suggestions = [];
            if ($articles->count() < 3) {
                $suggestions = $this->getSearchSuggestions($searchTerm);
            }

            return view('pages.blog.search', compact('articles', 'searchTerm', 'suggestions'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la recherche blog: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('blog.index'))
                ->with('error', __('Une erreur est survenue lors de la recherche.'));
        }
    }

    /**
     * Flux RSS du blog
     */
    public function rss()
    {
        try {
            $articles = Cache::remember('blog_rss', 1800, function () {
                return BlogArticle::publishedAndVisible()
                    ->with('media')
                    ->recent()
                    ->limit(20)
                    ->get();
            });

            return response()->view('pages.blog.rss', compact('articles'))
                ->header('Content-Type', 'application/rss+xml');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du flux RSS blog: ' . $e->getMessage());
            abort(503, __('Service temporairement indisponible'));
        }
    }

    /**
     * API pour charger plus d'articles (AJAX)
     */
    public function loadMore(Request $request)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'category' => 'sometimes|string',
            'search' => 'sometimes|string|max:100'
        ]);

        try {
            $perPage = 6;
            $page = $request->get('page');
            $category = $request->get('category');
            $search = $request->get('search');

            $query = BlogArticle::publishedAndVisible()->with('media');

            if ($category) {
                $query->where('categorie', $category);
            }

            if ($search) {
                $query->search($search);
            }

            $articles = $query->recent()
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'articles' => $articles->items(),
                'has_more' => $articles->hasMorePages(),
                'current_page' => $articles->currentPage(),
                'total' => $articles->total()
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement d\'articles supplémentaires: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Erreur lors du chargement')
            ], 500);
        }
    }

    /**
     * Méthodes privées
     */

    private function getCategoryName(string $category): string
    {
        $categories = [
            'coulisses' => __('Coulisses'),
            'conseils' => __('Conseils'),
            'actualites' => __('Actualités'),
            'interviews' => __('Interviews'),
            'tendances' => __('Tendances'),
            'lifestyle' => __('Lifestyle')
        ];

        return $categories[$category] ?? ucfirst($category);
    }

    private function getCategoryDescription(string $category): string
    {
        $descriptions = [
            'coulisses' => __('Découvrez les secrets de fabrication de nos épisodes et l\'envers du décor.'),
            'conseils' => __('Nos meilleurs conseils et astuces pour améliorer votre quotidien.'),
            'actualites' => __('Toute l\'actualité de L\'Astuce et les dernières nouvelles.'),
            'interviews' => __('Rencontres et interviews avec nos invités et partenaires.'),
            'tendances' => __('Les dernières tendances et nouveautés dans notre domaine.'),
            'lifestyle' => __('Inspiration lifestyle et bien-être pour une vie plus belle.')
        ];

        return $descriptions[$category] ?? '';
    }

    private function getRelatedTags(string $tag): array
    {
        // Logique pour trouver des tags similaires
        // Ici, on prend juste les tags les plus populaires pour l'exemple
        return Cache::remember("related_tags_{$tag}", 3600, function () {
            return BlogArticle::publishedAndVisible()
                ->whereNotNull('mots_cles')
                ->pluck('mots_cles')
                ->filter()
                ->flatMap(function ($tags) {
                    return is_string($tags) ? json_decode($tags, true) ?? [] : $tags;
                })
                ->countBy()
                ->sortDesc()
                ->take(10)
                ->keys()
                ->toArray();
        });
    }

    private function getSearchSuggestions(string $searchTerm): array
    {
        // Suggestions basées sur des mots-clés similaires
        $suggestions = [];
        
        // Articles avec des titres similaires
        $similarTitles = BlogArticle::publishedAndVisible()
            ->where('titre', 'like', '%' . substr($searchTerm, 0, 3) . '%')
            ->limit(3)
            ->pluck('titre')
            ->toArray();

        // Tags similaires
        $similarTags = Cache::remember('all_blog_tags', 3600, function () {
            return BlogArticle::publishedAndVisible()
                ->whereNotNull('mots_cles')
                ->pluck('mots_cles')
                ->filter()
                ->flatMap(function ($tags) {
                    return is_string($tags) ? json_decode($tags, true) ?? [] : $tags;
                })
                ->unique()
                ->values()
                ->toArray();
        });

        $matchingTags = array_filter($similarTags, function ($tag) use ($searchTerm) {
            return stripos($tag, $searchTerm) !== false;
        });

        return [
            'titles' => $similarTitles,
            'tags' => array_slice($matchingTags, 0, 5)
        ];
    }
} 