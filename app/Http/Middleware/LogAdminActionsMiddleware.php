<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminLog;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Logger seulement si l'utilisateur est connecté et admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            $this->logAction($request, $response);
        }

        return $response;
    }

    /**
     * Logger l'action admin
     */
    private function logAction(Request $request, Response $response): void
    {
        $user = Auth::user();
        $method = $request->method();
        $url = $request->fullUrl();
        $route = $request->route();
        
        // Déterminer l'action basée sur la méthode HTTP et la route
        $action = $this->determineAction($method, $route);
        
        // Déterminer le modèle affecté
        $modelInfo = $this->extractModelInfo($request, $route);
        
        // Déterminer la sévérité
        $severity = $this->determineSeverity($method, $response->getStatusCode());

        // Ne pas logger les actions de lecture simples (GET) sauf si c'est sensible
        if ($method === 'GET' && !$this->isSensitiveGetAction($route)) {
            return;
        }

        AdminLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $modelInfo['type'],
            'model_id' => $modelInfo['id'],
            'old_values' => $this->getOldValues($request),
            'new_values' => $this->getNewValues($request),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $url,
            'method' => $method,
            'description' => $this->generateDescription($action, $modelInfo, $user),
            'severity' => $severity,
        ]);
    }

    /**
     * Déterminer l'action basée sur la méthode et la route
     */
    private function determineAction(string $method, $route): string
    {
        if (!$route) {
            return strtolower($method);
        }

        $routeName = $route->getName() ?? '';
        $uri = $route->uri();

        // Actions spécifiques basées sur les noms de routes
        if (str_contains($routeName, '.store') || $method === 'POST') {
            return 'create';
        }
        
        if (str_contains($routeName, '.update') || $method === 'PUT' || $method === 'PATCH') {
            return 'update';
        }
        
        if (str_contains($routeName, '.destroy') || $method === 'DELETE') {
            return 'delete';
        }

        // Actions spéciales
        if (str_contains($routeName, 'login')) {
            return 'login';
        }
        
        if (str_contains($routeName, 'logout')) {
            return 'logout';
        }

        if (str_contains($routeName, 'moderate')) {
            return 'moderate';
        }

        if (str_contains($routeName, 'approve')) {
            return 'approve';
        }

        if (str_contains($routeName, 'reject')) {
            return 'reject';
        }

        return strtolower($method);
    }

    /**
     * Extraire les informations du modèle affecté
     */
    private function extractModelInfo(Request $request, $route): array
    {
        $modelType = null;
        $modelId = null;

        if (!$route) {
            return ['type' => null, 'id' => null];
        }

        $routeName = $route->getName() ?? '';
        $parameters = $route->parameters();

        // Déterminer le type de modèle basé sur la route
        if (str_contains($routeName, 'episodes')) {
            $modelType = 'App\Models\Episode';
            $model = $parameters['episode'] ?? $parameters['id'] ?? null;
            $modelId = ($model instanceof \Illuminate\Database\Eloquent\Model) ? $model->id : $model;
        } elseif (str_contains($routeName, 'astuces')) {
            $modelType = 'App\Models\AstucesSoumise';
            $model = $parameters['astuce'] ?? $parameters['id'] ?? null;
            $modelId = ($model instanceof \Illuminate\Database\Eloquent\Model) ? $model->id : $model;
        } elseif (str_contains($routeName, 'partenariats')) {
            $modelType = 'App\Models\Partenariat';
            $model = $parameters['partenariat'] ?? $parameters['id'] ?? null;
            $modelId = ($model instanceof \Illuminate\Database\Eloquent\Model) ? $model->id : $model;
        } elseif (str_contains($routeName, 'blog')) {
            $modelType = 'App\Models\BlogArticle';
            $model = $parameters['article'] ?? $parameters['id'] ?? null;
            $modelId = ($model instanceof \Illuminate\Database\Eloquent\Model) ? $model->id : $model;
        } elseif (str_contains($routeName, 'users')) {
            $modelType = 'App\Models\User';
            $model = $parameters['user'] ?? $parameters['id'] ?? null;
            $modelId = ($model instanceof \Illuminate\Database\Eloquent\Model) ? $model->id : $model;
        } elseif (str_contains($routeName, 'newsletter')) {
            $modelType = 'App\Models\NewsletterAbonne';
            $model = $parameters['abonne'] ?? $parameters['id'] ?? null;
            $modelId = ($model instanceof \Illuminate\Database\Eloquent\Model) ? $model->id : $model;
        }

        return ['type' => $modelType, 'id' => $modelId];
    }

    /**
     * Déterminer la sévérité de l'action
     */
    private function determineSeverity(string $method, int $statusCode): string
    {
        // Erreurs
        if ($statusCode >= 400) {
            return $statusCode >= 500 ? 'critical' : 'error';
        }

        // Actions critiques
        if ($method === 'DELETE') {
            return 'warning';
        }

        // Actions de modification
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return 'info';
        }

        return 'info';
    }

    /**
     * Vérifier si c'est une action GET sensible à logger
     */
    private function isSensitiveGetAction($route): bool
    {
        if (!$route) {
            return false;
        }

        $routeName = $route->getName() ?? '';
        
        $sensitiveActions = [
            'admin.logs',
            'admin.users',
            'admin.settings',
            'admin.export',
            'admin.backup',
        ];

        foreach ($sensitiveActions as $action) {
            if (str_contains($routeName, $action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtenir les anciennes valeurs (pour les mises à jour)
     */
    private function getOldValues(Request $request): ?array
    {
        // Pour les mises à jour, on pourrait récupérer les valeurs avant modification
        // Cela nécessiterait une logique plus complexe dans les contrôleurs
        return null;
    }

    /**
     * Obtenir les nouvelles valeurs
     */
    private function getNewValues(Request $request): ?array
    {
        $method = $request->method();
        
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $data = $request->all();
            
            // Filtrer les données sensibles
            $filtered = array_filter($data, function($key) {
                return !in_array($key, [
                    'password',
                    'password_confirmation',
                    '_token',
                    '_method',
                    'two_factor_secret',
                ]);
            }, ARRAY_FILTER_USE_KEY);

            return $filtered;
        }

        return null;
    }

    /**
     * Générer une description lisible de l'action
     */
    private function generateDescription(string $action, array $modelInfo, $user): string
    {
        $userName = $user->name;
        $modelName = $modelInfo['type'] ? class_basename($modelInfo['type']) : 'ressource';
        $modelId = $modelInfo['id'] ? "#{$modelInfo['id']}" : '';

        return match($action) {
            'create' => "{$userName} a créé un(e) {$modelName} {$modelId}",
            'update' => "{$userName} a modifié {$modelName} {$modelId}",
            'delete' => "{$userName} a supprimé {$modelName} {$modelId}",
            'moderate' => "{$userName} a modéré {$modelName} {$modelId}",
            'approve' => "{$userName} a approuvé {$modelName} {$modelId}",
            'reject' => "{$userName} a rejeté {$modelName} {$modelId}",
            'login' => "{$userName} s'est connecté à l'administration",
            'logout' => "{$userName} s'est déconnecté de l'administration",
            default => "{$userName} a effectué l'action '{$action}' sur {$modelName} {$modelId}",
        };
    }
}
