<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AstucesSoumise;

class AstuceAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AstucesSoumise::query();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre_astuce', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $astuces = $query->paginate(20)->withQueryString();

        // Statistiques pour les badges
        $stats = [
            'total' => AstucesSoumise::count(),
            'en_attente' => AstucesSoumise::where('status', 'en_attente')->count(),
            'approuve' => AstucesSoumise::where('status', 'approuve')->count(),
            'rejete' => AstucesSoumise::where('status', 'rejete')->count(),
        ];

        return view('admin.astuces.index', compact(
            'astuces',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.astuces.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'categorie' => ['required', 'string', 'max:100'],
            'difficulte' => ['required', 'in:facile,moyen,difficile'],
            'temps_estime' => ['nullable', 'integer', 'min:1'],
            'materiel_requis' => ['nullable', 'string'],
            'etapes' => ['required', 'array', 'min:1'],
            'etapes.*' => ['required', 'string'],
            'conseils' => ['nullable', 'string'],
            'nom_soumetteur' => ['required', 'string', 'max:255'],
            'email_soumetteur' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:en_attente,approuve,rejete'],
            'images.*' => ['nullable', 'image', 'max:2048'],
        ]);

        // Mapper les noms de champs vers les noms de colonnes
        $validated['titre_astuce'] = $validated['titre'];
        $validated['nom'] = $validated['nom_soumetteur'];
        $validated['email'] = $validated['email_soumetteur'];
        unset($validated['titre'], $validated['nom_soumetteur'], $validated['email_soumetteur']);

        // Traitement des images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('astuces', 'public');
                $images[] = $path;
            }
        }
        $validated['images'] = $images;

        $astuce = AstucesSoumise::create($validated);

        return redirect()->route('admin.astuces.index')
            ->with('success', 'Astuce créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AstucesSoumise $astuce)
    {
        return view('admin.astuces.show', compact('astuce'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AstucesSoumise $astuce)
    {
        return view('admin.astuces.edit', compact('astuce'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AstucesSoumise $astuce)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'categorie' => ['required', 'string', 'max:100'],
            'difficulte' => ['required', 'in:facile,moyen,difficile'],
            'temps_estime' => ['nullable', 'integer', 'min:1'],
            'materiel_requis' => ['nullable', 'string'],
            'etapes' => ['required', 'array', 'min:1'],
            'etapes.*' => ['required', 'string'],
            'conseils' => ['nullable', 'string'],
            'nom_soumetteur' => ['required', 'string', 'max:255'],
            'email_soumetteur' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:en_attente,approuve,rejete'],
            'commentaire_admin' => ['nullable', 'string'],
            'images.*' => ['nullable', 'image', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
        ]);

        // Mapper les noms de champs vers les noms de colonnes
        $validated['titre_astuce'] = $validated['titre'];
        $validated['nom'] = $validated['nom_soumetteur'];
        $validated['email'] = $validated['email_soumetteur'];
        unset($validated['titre'], $validated['nom_soumetteur'], $validated['email_soumetteur']);

        // Gestion des images
        $images = $astuce->images ?? [];

        // Supprimer les images sélectionnées
        if ($request->filled('remove_images')) {
            foreach ($request->remove_images as $imageToRemove) {
                if (($key = array_search($imageToRemove, $images)) !== false) {
                    Storage::disk('public')->delete($imageToRemove);
                    unset($images[$key]);
                }
            }
            $images = array_values($images); // Réindexer
        }

        // Ajouter les nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('astuces', 'public');
                $images[] = $path;
            }
        }

        $validated['images'] = $images;

        $astuce->update($validated);

        return redirect()->route('admin.astuces.index')
            ->with('success', 'Astuce mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AstucesSoumise $astuce)
    {
        // Supprimer les images associées
        if ($astuce->images) {
            foreach ($astuce->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $astuce->delete();

        return redirect()->route('admin.astuces.index')
            ->with('success', 'Astuce supprimée avec succès.');
    }

    /**
     * Approuver une astuce
     */
    public function approve(Request $request, AstucesSoumise $astuce)
    {
        $request->validate([
            'commentaire_admin' => ['nullable', 'string'],
            'send_notification' => ['boolean'],
        ]);

        $astuce->update([
            'status' => 'approuve',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Astuce approuvée avec succès.',
                'status' => 'approuve',
            ]);
        }

        return redirect()->route('admin.astuces.index')
            ->with('success', 'Astuce approuvée avec succès.');
    }

    /**
     * Rejeter une astuce
     */
    public function reject(Request $request, AstucesSoumise $astuce)
    {
        $request->validate([
            'commentaire_admin' => ['required', 'string'],
            'send_notification' => ['boolean'],
        ]);

        $astuce->update([
            'status' => 'rejete',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Astuce rejetée avec succès.',
                'status' => 'rejete',
            ]);
        }

        return redirect()->route('admin.astuces.index')
            ->with('success', 'Astuce rejetée avec succès.');
    }

    /**
     * Actions en lot
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject,delete'],
            'astuces' => ['required', 'array', 'min:1'],
            'astuces.*' => ['exists:astuces_soumises,id'],
            'commentaire_admin' => ['nullable', 'string'],
        ]);

        $astuces = AstucesSoumise::whereIn('id', $request->astuces)->get();
        $count = 0;

        foreach ($astuces as $astuce) {
            switch ($request->action) {
                case 'approve':
                    if ($astuce->status !== 'approuve') {
                        $astuce->update([
                            'status' => 'approuve',
                            'commentaire_admin' => $request->commentaire_admin,
                        ]);
                        $count++;
                    }
                    break;

                case 'reject':
                    if ($astuce->status !== 'rejete') {
                        $astuce->update([
                            'status' => 'rejete',
                            'commentaire_admin' => $request->commentaire_admin,
                        ]);
                        $count++;
                    }
                    break;

                case 'delete':
                    // Supprimer les images
                    if ($astuce->images) {
                        foreach ($astuce->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                    $astuce->delete();
                    $count++;
                    break;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Action '{$request->action}' effectuée sur {$count} astuce(s).",
                'count' => $count,
            ]);
        }

        return redirect()->route('admin.astuces.index')
            ->with('success', "Action '{$request->action}' effectuée sur {$count} astuce(s).");
    }

    /**
     * Exporter les astuces
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => ['required', 'in:csv,json'],
            'status' => ['nullable', 'in:en_attente,approuve,rejete'],
        ]);

        $query = AstucesSoumise::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $astuces = $query->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($astuces);
        }

        return response()->json($astuces);
    }

    /**
     * Exporter vers CSV
     */
    private function exportToCsv($astuces)
    {
        $filename = 'astuces_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($astuces) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Titre', 'Auteur', 'Email', 'Statut',
                'Date création', 'Description (extrait)'
            ]);

            // Données
            foreach ($astuces as $astuce) {
                fputcsv($file, [
                    $astuce->id,
                    $astuce->titre_astuce,
                    $astuce->nom,
                    $astuce->email,
                    $astuce->status,
                    $astuce->created_at->format('d/m/Y H:i'),
                    \Str::limit($astuce->description, 100),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}