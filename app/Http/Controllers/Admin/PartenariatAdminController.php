<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partenariat;

class PartenariatAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Partenariat::query();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_entreprise', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
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

        $partenariats = $query->paginate(20)->withQueryString();

        // Statistiques pour les badges
        $stats = [
            'total' => Partenariat::count(),
            'nouveau' => Partenariat::where('status', 'nouveau')->count(),
            'en_cours' => Partenariat::where('status', 'en_cours')->count(),
            'accepte' => Partenariat::where('status', 'accepte')->count(),
            'refuse' => Partenariat::where('status', 'refuse')->count(),
        ];

        return view('admin.partenariats.index', compact(
            'partenariats',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.partenariats.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:20'],
            'status' => ['required', 'in:nouveau,en_cours,accepte,refuse'],
            'notes_internes' => ['nullable', 'string'],
        ]);

        $partenariat = Partenariat::create($validated);

        return redirect()->route('admin.partenariats.index')
            ->with('success', 'Partenariat créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Partenariat $partenariat)
    {
        return view('admin.partenariats.show', compact('partenariat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partenariat $partenariat)
    {
        return view('admin.partenariats.edit', compact('partenariat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partenariat $partenariat)
    {
        $validated = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:20'],
            'status' => ['required', 'in:nouveau,en_cours,accepte,refuse'],
            'notes_internes' => ['nullable', 'string'],
        ]);

        $partenariat->update($validated);

        return redirect()->route('admin.partenariats.index')
            ->with('success', 'Partenariat mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partenariat $partenariat)
    {
        $partenariat->delete();

        return redirect()->route('admin.partenariats.index')
            ->with('success', 'Partenariat supprimé avec succès.');
    }

    /**
     * Accepter un partenariat
     */
    public function approve(Request $request, Partenariat $partenariat)
    {
        $request->validate([
            'notes_internes' => ['nullable', 'string'],
            'send_notification' => ['boolean'],
        ]);

        $partenariat->update([
            'status' => 'accepte',
            'notes_internes' => $request->notes_internes,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partenariat accepté avec succès.',
                'status' => 'accepte',
            ]);
        }

        return redirect()->route('admin.partenariats.index')
            ->with('success', 'Partenariat accepté avec succès.');
    }

    /**
     * Refuser un partenariat
     */
    public function reject(Request $request, Partenariat $partenariat)
    {
        $request->validate([
            'notes_internes' => ['required', 'string'],
            'send_notification' => ['boolean'],
        ]);

        $partenariat->update([
            'status' => 'refuse',
            'notes_internes' => $request->notes_internes,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partenariat refusé avec succès.',
                'status' => 'refuse',
            ]);
        }

        return redirect()->route('admin.partenariats.index')
            ->with('success', 'Partenariat refusé avec succès.');
    }

    /**
     * Actions en lot
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject,delete,en_cours'],
            'partenariats' => ['required', 'array', 'min:1'],
            'partenariats.*' => ['exists:partenariats,id'],
            'notes_internes' => ['nullable', 'string'],
        ]);

        $partenariats = Partenariat::whereIn('id', $request->partenariats)->get();
        $count = 0;

        foreach ($partenariats as $partenariat) {
            switch ($request->action) {
                case 'approve':
                    if ($partenariat->status !== 'accepte') {
                        $partenariat->update([
                            'status' => 'accepte',
                            'notes_internes' => $request->notes_internes,
                        ]);
                        $count++;
                    }
                    break;

                case 'reject':
                    if ($partenariat->status !== 'refuse') {
                        $partenariat->update([
                            'status' => 'refuse',
                            'notes_internes' => $request->notes_internes,
                        ]);
                        $count++;
                    }
                    break;

                case 'en_cours':
                    if ($partenariat->status !== 'en_cours') {
                        $partenariat->update([
                            'status' => 'en_cours',
                            'notes_internes' => $request->notes_internes,
                        ]);
                        $count++;
                    }
                    break;

                case 'delete':
                    $partenariat->delete();
                    $count++;
                    break;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Action '{$request->action}' effectuée sur {$count} partenariat(s).",
                'count' => $count,
            ]);
        }

        return redirect()->route('admin.partenariats.index')
            ->with('success', "Action '{$request->action}' effectuée sur {$count} partenariat(s).");
    }

    /**
     * Exporter les partenariats
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => ['required', 'in:csv,json'],
            'status' => ['nullable', 'in:nouveau,en_cours,accepte,refuse'],
        ]);

        $query = Partenariat::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $partenariats = $query->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($partenariats);
        }

        return response()->json($partenariats);
    }

    /**
     * Exporter vers CSV
     */
    private function exportToCsv($partenariats)
    {
        $filename = 'partenariats_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($partenariats) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Entreprise', 'Contact', 'Email', 'Statut',
                'Date création', 'Message (extrait)'
            ]);

            // Données
            foreach ($partenariats as $partenariat) {
                fputcsv($file, [
                    $partenariat->id,
                    $partenariat->nom_entreprise,
                    $partenariat->contact,
                    $partenariat->email,
                    $partenariat->status,
                    $partenariat->created_at->format('d/m/Y H:i'),
                    \Str::limit($partenariat->message, 100),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}