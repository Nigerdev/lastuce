<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\FailedLoginAttempt;
use App\Models\AdminNotification;
use App\Services\NotificationService;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Gestion des utilisateurs
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'locked') {
                $query->where('locked_until', '>', now());
            } elseif ($request->status === 'active') {
                $query->where(function ($q) {
                    $q->whereNull('locked_until')
                      ->orWhere('locked_until', '<=', now());
                });
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'locked' => User::where('locked_until', '>', now())->count(),
            'recent' => User::where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Créer un utilisateur
     */
    public function createUser()
    {
        $roles = User::getAvailableRoles();
        $permissions = User::getAvailablePermissions();
        
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,moderator'],
            'permissions' => ['array'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
            'role' => $request->role,
            'permissions' => $request->permissions ?? [],
        ]);

        // Logger la création
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => $user->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => auth()->user()->name . " a créé l'utilisateur {$user->name}",
            'severity' => 'info',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Modifier un utilisateur
     */
    public function editUser(User $user)
    {
        $roles = User::getAvailableRoles();
        $permissions = User::getAvailablePermissions();
        
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,moderator'],
            'permissions' => ['array'],
        ]);

        $oldValues = $user->toArray();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => $request->permissions ?? [],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Logger la modification
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => auth()->user()->name . " a modifié l'utilisateur {$user->name}",
            'severity' => 'info',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroyUser(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Vous ne pouvez pas supprimer votre propre compte.'], 400);
        }

        $oldValues = $user->toArray();
        $user->delete();

        // Logger l'action
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => auth()->user()->name . " a supprimé l'utilisateur {$user->name}",
            'severity' => 'warning',
        ]);

        return response()->json(['success' => true, 'message' => 'Utilisateur supprimé avec succès.']);
    }

    /**
     * Logs de sécurité
     */
    public function logs(Request $request)
    {
        $query = AdminLog::with('user');

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        $stats = [
            'total' => AdminLog::count(),
            'today' => AdminLog::whereDate('created_at', today())->count(),
            'critical' => AdminLog::where('severity', 'critical')->count(),
            'errors' => AdminLog::where('severity', 'error')->count(),
        ];

        $users = User::admins()->get(['id', 'name']);
        $actions = AdminLog::distinct()->pluck('action');
        $severities = ['info', 'warning', 'error', 'critical'];

        return view('admin.security.logs', compact('logs', 'stats', 'users', 'actions', 'severities'));
    }

    /**
     * Tentatives de connexion échouées
     */
    public function failedAttempts(Request $request)
    {
        $query = FailedLoginAttempt::query();

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', "%{$request->ip}%");
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $attempts = $query->orderBy('attempted_at', 'desc')->paginate(50)->withQueryString();

        $stats = [
            'total' => FailedLoginAttempt::count(),
            'today' => FailedLoginAttempt::whereDate('attempted_at', today())->count(),
            'last_hour' => FailedLoginAttempt::where('attempted_at', '>=', now()->subHour())->count(),
            'blocked_ips' => FailedLoginAttempt::where('attempted_at', '>=', now()->subHour())
                ->groupBy('ip_address')
                ->havingRaw('COUNT(*) >= 5')
                ->distinct('ip_address')
                ->count('ip_address'),
        ];

        return view('admin.security.failed-attempts', compact('attempts', 'stats'));
    }

    /**
     * Obtenir les notifications
     */
    public function getNotifications(Request $request)
    {
        $notificationService = app(NotificationService::class);
        $notifications = $notificationService->getUnreadForUser(auth()->id(), 20);

        return response()->json($notifications);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationRead(AdminNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function lockUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Vous ne pouvez pas verrouiller votre propre compte.'], 400);
        }

        $user->lockAccount(24); // Verrouiller pour 24 heures

        // Logger l'action
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'lock',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['locked_until' => $user->locked_until],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => auth()->user()->name . " a verrouillé le compte de {$user->name}",
            'severity' => 'warning',
        ]);

        return response()->json(['success' => true, 'message' => 'Compte verrouillé avec succès.']);
    }

    public function unlockUser(User $user)
    {
        $user->unlockAccount();

        // Logger l'action
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'unlock',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => ['locked_until' => $user->getOriginal('locked_until')],
            'new_values' => ['locked_until' => null],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => auth()->user()->name . " a déverrouillé le compte de {$user->name}",
            'severity' => 'info',
        ]);

        return response()->json(['success' => true, 'message' => 'Compte déverrouillé avec succès.']);
    }

    public function bulkUserAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:lock,unlock,delete'],
            'users' => ['required', 'array', 'min:1'],
            'users.*' => ['exists:users,id'],
        ]);

        $users = User::whereIn('id', $request->users)->get();
        $count = 0;

        foreach ($users as $user) {
            if ($user->id === auth()->id()) {
                continue; // Ignorer son propre compte
            }

            switch ($request->action) {
                case 'lock':
                    $user->lockAccount(24);
                    $count++;
                    break;
                case 'unlock':
                    $user->unlockAccount();
                    $count++;
                    break;
                case 'delete':
                    $user->delete();
                    $count++;
                    break;
            }
        }

        // Logger l'action en lot
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_' . $request->action,
            'model_type' => User::class,
            'new_values' => [
                'action' => $request->action,
                'count' => $count,
                'user_ids' => $request->users,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => auth()->user()->name . " a effectué l'action '{$request->action}' sur {$count} utilisateur(s)",
            'severity' => $request->action === 'delete' ? 'warning' : 'info',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Action '{$request->action}' effectuée sur {$count} utilisateur(s).",
            'count' => $count,
        ]);
    }

    public function blockedIps(Request $request)
    {
        // Récupérer les IPs bloquées (plus de 5 tentatives dans la dernière heure)
        $blockedIps = FailedLoginAttempt::select('ip_address')
            ->selectRaw('COUNT(*) as attempts')
            ->selectRaw('MAX(attempted_at) as last_attempt')
            ->where('attempted_at', '>=', now()->subHour())
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= 5')
            ->orderBy('attempts', 'desc')
            ->paginate(20);

        return view('admin.security.blocked-ips', compact('blockedIps'));
    }

    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'ip'],
        ]);

        // Supprimer les tentatives récentes pour cette IP
        FailedLoginAttempt::where('ip_address', $request->ip_address)
            ->where('attempted_at', '>=', now()->subHour())
            ->delete();

        // Logger l'action
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'unblock_ip',
            'new_values' => ['ip_address' => $request->ip_address],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => auth()->user()->name . " a débloqué l'IP {$request->ip_address}",
            'severity' => 'warning',
        ]);

        return response()->json(['success' => true, 'message' => 'IP débloquée avec succès.']);
    }

    public function deleteLog(AdminLog $log)
    {
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Log supprimé avec succès.']);
    }

    public function cleanupLogs(Request $request)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $deleted = AdminLog::where('created_at', '<', now()->subDays($request->days))->delete();

        // Logger l'action
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'cleanup_logs',
            'new_values' => ['days' => $request->days, 'deleted_count' => $deleted],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => auth()->user()->name . " a nettoyé {$deleted} log(s) de plus de {$request->days} jour(s)",
            'severity' => 'info',
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$deleted} log(s) supprimé(s) avec succès.",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Paramètres
     */
    public function settings()
    {
        $settings = Cache::get('app_settings', []);
        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['required', 'string', 'max:500'],
            'contact_email' => ['required', 'email'],
            'maintenance_mode' => ['boolean'],
        ]);

        $settings = $request->only([
            'site_name',
            'site_description',
            'contact_email',
            'maintenance_mode',
        ]);

        Cache::put('app_settings', $settings, now()->addDays(30));

        // Logger l'action
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_settings',
            'new_values' => $settings,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => auth()->user()->name . " a mis à jour les paramètres du site",
            'severity' => 'info',
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function backup()
    {
        $backups = collect(Storage::disk('local')->files('backups'))
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'size' => Storage::disk('local')->size($file),
                    'date' => Storage::disk('local')->lastModified($file),
                ];
            })
            ->sortByDesc('date');

        return view('admin.settings.backup', compact('backups'));
    }

    public function createBackup(Request $request)
    {
        try {
            Artisan::call('backup:run');
            
            // Logger l'action
            AdminLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_backup',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => auth()->user()->name . " a créé une sauvegarde",
                'severity' => 'info',
            ]);

            return response()->json(['success' => true, 'message' => 'Sauvegarde créée avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création de la sauvegarde: ' . $e->getMessage()], 500);
        }
    }

    public function maintenance()
    {
        $isMaintenanceMode = app()->isDownForMaintenance();
        return view('admin.settings.maintenance', compact('isMaintenanceMode'));
    }

    public function toggleMaintenance(Request $request)
    {
        try {
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
                $action = 'disabled';
            } else {
                Artisan::call('down', ['--secret' => 'admin-access']);
                $action = 'enabled';
            }

            // Logger l'action
            AdminLog::create([
                'user_id' => auth()->id(),
                'action' => 'toggle_maintenance',
                'new_values' => ['maintenance_mode' => $action],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => auth()->user()->name . " a {$action} le mode maintenance",
                'severity' => 'warning',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Mode maintenance {$action} avec succès.",
                'maintenance_mode' => $action === 'enabled',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du changement de mode: ' . $e->getMessage()], 500);
        }
    }
}
