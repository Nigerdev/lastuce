<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Episode;

class EpisodeAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Episode::query();

        // Filtres
        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $episodes = $query->paginate(20)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Episode::count(),
            'published' => Episode::where('statut', 'published')->count(),
            'draft' => Episode::where('statut', 'draft')->count(),
            'scheduled' => Episode::where('statut', 'scheduled')->count(),
        ];

        return view('admin.episodes.index', compact('episodes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return response()->json([
                'success' => true,
                'episode' => new Episode()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in create episode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Store episode request data:', $request->all());
            
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'statut' => 'required|in:draft,published,scheduled',
                'duree' => 'nullable|integer|min:0',
                'youtube_url' => 'nullable|url',
                'date_publication' => 'nullable|date',
                'vues' => 'nullable|integer|min:0'
            ]);

            // Générer automatiquement la thumbnail_url à partir de youtube_url
            if (!empty($validated['youtube_url'])) {
                $videoId = $this->extractYoutubeVideoId($validated['youtube_url']);
                if ($videoId) {
                    $validated['thumbnail_url'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
                }
            }

            $episode = Episode::create($validated);
            
            \Log::info('Episode created successfully:', ['id' => $episode->id]);

            return response()->json([
                'success' => true,
                'message' => 'Épisode créé avec succès',
                'episode' => $episode
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in store episode:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in store episode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            \Log::info('Show episode:', ['id' => $id]);
            $episode = Episode::findOrFail($id);
            return response()->json([
                'success' => true,
                'episode' => $episode
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Episode not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Épisode introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in show episode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'affichage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            \Log::info('Edit episode:', ['id' => $id]);
            $episode = Episode::findOrFail($id);
            return response()->json([
                'success' => true,
                'episode' => $episode
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Episode not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Épisode introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in edit episode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'édition: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('Update episode request data:', $request->all());
            
            $episode = Episode::findOrFail($id);
            
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'statut' => 'required|in:draft,published,scheduled',
                'duree' => 'nullable|integer|min:0',
                'youtube_url' => 'nullable|url',
                'date_publication' => 'nullable|date',
                'vues' => 'nullable|integer|min:0'
            ]);

            // Générer automatiquement la thumbnail_url à partir de youtube_url
            if (!empty($validated['youtube_url'])) {
                $videoId = $this->extractYoutubeVideoId($validated['youtube_url']);
                if ($videoId) {
                    $validated['thumbnail_url'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
                }
            }

            $episode->update($validated);
            
            \Log::info('Episode updated successfully:', ['id' => $episode->id]);

            return response()->json([
                'success' => true,
                'message' => 'Épisode mis à jour avec succès',
                'episode' => $episode
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Episode not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Épisode introuvable'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in update episode:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in update episode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            \Log::info('Delete episode:', ['id' => $id]);
            $episode = Episode::findOrFail($id);
            $episode->delete();

            return response()->json([
                'success' => true,
                'message' => 'Épisode supprimé avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Episode not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Épisode introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in destroy episode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk actions on episodes.
     */
    public function bulkAction(Request $request)
    {
        try {
            \Log::info('Bulk action request:', $request->all());
            
            $validated = $request->validate([
                'action' => 'required|in:publish,draft,delete',
                'episodes' => 'required|array',
                'episodes.*' => 'exists:episodes,id'
            ]);

            $episodes = Episode::whereIn('id', $validated['episodes']);
            $count = $episodes->count();

            switch ($validated['action']) {
                case 'publish':
                    $episodes->update(['statut' => 'published']);
                    $message = "{$count} épisode(s) publié(s) avec succès";
                    break;
                case 'draft':
                    $episodes->update(['statut' => 'draft']);
                    $message = "{$count} épisode(s) mis en brouillon avec succès";
                    break;
                case 'delete':
                    $episodes->delete();
                    $message = "{$count} épisode(s) supprimé(s) avec succès";
                    break;
            }
            
            \Log::info('Bulk action completed:', ['action' => $validated['action'], 'count' => $count]);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in bulk action:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in bulk action: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'action groupée: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYoutubeVideoId($url)
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}
