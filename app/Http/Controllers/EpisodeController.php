<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Episode;
use Illuminate\Support\Facades\Cache;

class EpisodeController extends Controller
{
    /**
     * Liste paginée des épisodes avec filtres
     */
    public function index(Request $request)
    {
        try {
            $perPage = 12;
            $sortBy = $request->get('sort', 'recent');
            $typeFilter = $request->get('type', 'all');
            $search = $request->get('search');

            // Construire la requête
            $query = Episode::published();

            // Filtres par type
            if ($typeFilter !== 'all') {
                switch ($typeFilter) {
                    case 'episode':
                        $query->where('type', 'episode');
                        break;
                    case 'coulisse':
                        $query->where('type', 'coulisse');
                        break;
                    case 'bonus':
                        $query->where('type', 'bonus');
                        break;
                    case 'special':
                        $query->where('type', 'special');
                        break;
                }
            }

            // Recherche
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Tri
            switch ($sortBy) {
                case 'recent':
                    $query->orderBy('date_publication', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('date_publication', 'asc');
                    break;
                case 'popular':
                    $query->orderBy('vues', 'desc');
                    break;
                case 'title':
                    $query->orderBy('titre', 'asc');
                    break;
                default:
                    $query->orderBy('date_publication', 'desc');
            }

            // Pagination
            $episodes = $query->paginate($perPage);

            // Statistiques pour les filtres
            $stats = [
                'total' => Episode::published()->count(),
                'episode' => Episode::published()->where('type', 'episode')->count(),
                'coulisse' => Episode::published()->where('type', 'coulisse')->count(),
                'bonus' => Episode::published()->where('type', 'bonus')->count(),
                'special' => Episode::published()->where('type', 'special')->count(),
            ];

            // Types disponibles
            $types = [
                'all' => __('Tous les épisodes'),
                'episode' => __('Épisodes'),
                'coulisse' => __('Coulisses'),
                'bonus' => __('Bonus'),
                'special' => __('Spéciaux'),
            ];

            // Options de tri
            $sortOptions = [
                'recent' => __('Plus récents'),
                'oldest' => __('Plus anciens'),
                'popular' => __('Plus populaires'),
                'title' => __('Titre A-Z'),
            ];

            return view('pages.episodes.index', compact(
                'episodes',
                'stats',
                'types',
                'sortOptions',
                'typeFilter',
                'sortBy',
                'search'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage des épisodes: ' . $e->getMessage());
            
            // En cas d'erreur, retourner une collection vide
            $episodes = collect();
            $stats = ['total' => 0, 'episode' => 0, 'coulisse' => 0, 'bonus' => 0, 'special' => 0];
            $types = [];
            $sortOptions = [];
            $typeFilter = 'all';
            $sortBy = 'recent';
            $search = '';
            
            return view('pages.episodes.index', compact(
                'episodes',
                'stats',
                'types',
                'sortOptions',
                'typeFilter',
                'sortBy',
                'search'
            ));
        }
    }

    /**
     * Affichage détaillé d'un épisode
     */
    public function show(Request $request, $slug)
    {
        try {
            $episode = Episode::where('slug', $slug)
                ->published()
                ->with(['media'])
                ->firstOrFail();

            // Incrémenter le compteur de vues (throttle par IP)
            $cacheKey = 'episode_view_' . $episode->id . '_' . $request->ip();
            if (!Cache::has($cacheKey)) {
                $episode->increment('vues');
                Cache::put($cacheKey, true, 3600); // 1 heure
            }

            // Épisodes suggérés (même type, excluant l'actuel)
            $suggestedEpisodes = Episode::published()
                ->where('type', $episode->type)
                ->where('id', '!=', $episode->id)
                ->recent()
                ->limit(4)
                ->get();

            // Si pas assez d'épisodes du même type, compléter avec d'autres
            if ($suggestedEpisodes->count() < 4) {
                $additionalEpisodes = Episode::published()
                    ->where('id', '!=', $episode->id)
                    ->whereNotIn('id', $suggestedEpisodes->pluck('id'))
                    ->recent()
                    ->limit(4 - $suggestedEpisodes->count())
                    ->get();
                
                $suggestedEpisodes = $suggestedEpisodes->concat($additionalEpisodes);
            }

            // Episode précédent et suivant
            $previousEpisode = Episode::published()
                ->where('date_publication', '<', $episode->date_publication)
                ->orderBy('date_publication', 'desc')
                ->first();

            $nextEpisode = Episode::published()
                ->where('date_publication', '>', $episode->date_publication)
                ->orderBy('date_publication', 'asc')
                ->first();

            // Meta données pour le SEO
            $seoData = [
                'title' => $episode->titre . ' - L\'Astuce',
                'description' => $episode->description ?: str_limit(strip_tags($episode->contenu), 160),
                'image' => $episode->thumbnail_url,
                'url' => route('episodes.show', $episode->slug),
                'type' => 'video',
                'published_time' => $episode->date_publication ? $episode->date_publication->toISOString() : null,
                'video_url' => $episode->youtube_url,
                'duration' => $episode->duree
            ];

            return view('pages.episodes.show', compact(
                'episode',
                'suggestedEpisodes',
                'previousEpisode',
                'nextEpisode',
                'seoData'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, __('Épisode introuvable'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage de l\'épisode: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'))
                ->with('error', __('Une erreur est survenue lors du chargement de l\'épisode.'));
        }
    }

    /**
     * API pour charger plus d'épisodes (AJAX)
     */
    public function loadMore(Request $request)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'type' => 'sometimes|string|in:all,episode,coulisse,bonus',
            'search' => 'sometimes|string|max:100'
        ]);

        try {
            $perPage = 6;
            $page = $request->get('page');
            $typeFilter = $request->get('type', 'all');
            $search = $request->get('search');

            $query = Episode::published()->with('media');

            // Filtres
            if ($typeFilter !== 'all') {
                switch ($typeFilter) {
                    case 'episode':
                        $query->episodes();
                        break;
                    case 'coulisse':
                        $query->coulisses();
                        break;
                    case 'bonus':
                        $query->bonus();
                        break;
                }
            }

            if ($search) {
                $query->search($search);
            }

            $episodes = $query->recent()
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'episodes' => $episodes->items(),
                'has_more' => $episodes->hasMorePages(),
                'current_page' => $episodes->currentPage(),
                'total' => $episodes->total()
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement d\'épisodes supplémentaires: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Erreur lors du chargement')
            ], 500);
        }
    }

    /**
     * Flux RSS des épisodes
     */
    public function rss()
    {
        try {
            $episodes = Cache::remember('episodes_rss', 1800, function () {
                return Episode::published()
                    ->with('media')
                    ->recent()
                    ->limit(20)
                    ->get();
            });

            return response()->view('pages.episodes.rss', compact('episodes'))
                ->header('Content-Type', 'application/rss+xml');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du flux RSS: ' . $e->getMessage());
            abort(503, __('Service temporairement indisponible'));
        }
    }

    /**
     * Archive des épisodes par année/mois
     */
    public function archive(Request $request, $year = null, $month = null)
    {
        try {
            $query = Episode::published();

            if ($year) {
                $query->whereYear('date_diffusion', $year);
                
                if ($month) {
                    $query->whereMonth('date_diffusion', $month);
                }
            }

            $episodes = $query->with('media')
                ->recent()
                ->paginate(12);

            // Années et mois disponibles pour la navigation
            $archiveData = Cache::remember('episodes_archive_data', 3600, function () {
                return Episode::published()
                    ->selectRaw('YEAR(date_diffusion) as year, MONTH(date_diffusion) as month, COUNT(*) as count')
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get()
                    ->groupBy('year');
            });

            return view('pages.episodes.archive', compact(
                'episodes',
                'archiveData',
                'year',
                'month'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage des archives: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'))
                ->with('error', __('Une erreur est survenue lors du chargement des archives.'));
        }
    }

    /**
     * Recherche avancée d'épisodes
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'sometimes|string|in:all,episode,coulisse,bonus',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'duration_min' => 'sometimes|integer|min:1',
            'duration_max' => 'sometimes|integer|gte:duration_min'
        ]);

        try {
            $query = Episode::published()->with('media');
            
            // Recherche textuelle
            $searchTerm = $request->get('q');
            $query->search($searchTerm);

            // Filtres avancés
            if ($request->filled('type') && $request->get('type') !== 'all') {
                $type = $request->get('type');
                switch ($type) {
                    case 'episode':
                        $query->episodes();
                        break;
                    case 'coulisse':
                        $query->coulisses();
                        break;
                    case 'bonus':
                        $query->bonus();
                        break;
                }
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date_diffusion', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date_diffusion', '<=', $request->get('date_to'));
            }

            if ($request->filled('duration_min')) {
                $query->where('duree', '>=', $request->get('duration_min'));
            }

            if ($request->filled('duration_max')) {
                $query->where('duree', '<=', $request->get('duration_max'));
            }

            $episodes = $query->recent()->paginate(12)->withQueryString();

            return view('pages.episodes.search', compact('episodes', 'searchTerm'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la recherche d\'épisodes: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'))
                ->with('error', __('Une erreur est survenue lors de la recherche.'));
        }
    }

    /**
     * Playlist d'épisodes (pour écoute continue)
     */
    public function playlist(Request $request, $type = 'all')
    {
        try {
            $query = Episode::published()
                ->whereNotNull('youtube_url')
                ->with('media');

            switch ($type) {
                case 'episode':
                    $query->episodes();
                    break;
                case 'coulisse':
                    $query->coulisses();
                    break;
                case 'bonus':
                    $query->bonus();
                    break;
                case 'recent':
                    $query->where('date_diffusion', '>=', now()->subMonths(3));
                    break;
                case 'popular':
                    $query->orderBy('vues', 'desc');
                    break;
                default:
                    // Tous les épisodes
                    break;
            }

            if ($type !== 'popular') {
                $query->recent();
            }

            $episodes = $query->get();

            return view('pages.episodes.playlist', compact('episodes', 'type'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération de la playlist: ' . $e->getMessage());
            
            return redirect(\App\Helpers\LocalizationHelper::getLocalizedRoute('episodes.index'))
                ->with('error', __('Une erreur est survenue lors de la génération de la playlist.'));
        }
    }
} 