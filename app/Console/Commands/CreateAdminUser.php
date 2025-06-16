<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user 
                            {--name= : Admin name}
                            {--email= : Admin email}
                            {--password= : Admin password}
                            {--role=admin : User role (admin, moderator)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création d\'un nouvel utilisateur administrateur');

        // Récupérer les données
        $name = $this->option('name') ?: $this->ask('Nom complet');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Mot de passe');
        $role = $this->option('role');

        // Validation
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,moderator'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        // Vérifier si l'email existe déjà
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Un utilisateur avec l'email {$email} existe déjà");
            return Command::FAILURE;
        }

        // Définir les permissions selon le rôle
        $permissions = $this->getPermissionsForRole($role);

        // Créer l'utilisateur
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'role' => $role,
                'permissions' => $permissions,
                'email_verified_at' => now(),
            ]);

            $this->info("✅ Utilisateur administrateur créé avec succès:");
            $this->line("   Nom: {$user->name}");
            $this->line("   Email: {$user->email}");
            $this->line("   Rôle: {$user->display_role}");
            $this->line("   Permissions: " . count($permissions) . " permission(s)");

            // Afficher les permissions si demandé
            if ($this->confirm('Afficher les permissions accordées?', false)) {
                $this->table(['Permission', 'Description'], 
                    collect($permissions)->map(function ($permission) {
                        $availablePermissions = User::getAvailablePermissions();
                        return [$permission, $availablePermissions[$permission] ?? 'Description non disponible'];
                    })->toArray()
                );
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la création: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Obtenir les permissions pour un rôle donné
     */
    private function getPermissionsForRole(string $role): array
    {
        $allPermissions = array_keys(User::getAvailablePermissions());

        return match($role) {
            'admin' => $allPermissions, // Toutes les permissions
            'moderator' => [
                'astuces.moderate',
                'astuces.delete',
                'partenariats.manage',
                'newsletter.manage',
                'logs.view',
            ],
            default => []
        };
    }
}
