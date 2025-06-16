<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeTranslatableMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:translatable-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migrations to make existing models translatable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating translatable migrations...');

        // Migrations pour les différents modèles
        $migrations = [
            'episodes' => [
                'fields' => ['titre', 'description', 'seo_title', 'seo_description', 'seo_keywords'],
                'table' => 'episodes',
            ],
            'blog_articles' => [
                'fields' => ['titre', 'contenu', 'resume', 'seo_title', 'seo_description', 'seo_keywords'],
                'table' => 'blog_articles',
            ],
            'astuces_soumises' => [
                'fields' => ['titre', 'description', 'conseils_supplementaires'],
                'table' => 'astuces_soumises',
            ],
        ];

        foreach ($migrations as $model => $config) {
            $this->createMigration($model, $config);
        }

        $this->info('All translatable migrations created successfully!');
        $this->line('Run "php artisan migrate" to apply the changes.');
    }

    /**
     * Créer une migration pour un modèle spécifique
     */
    private function createMigration($model, $config)
    {
        $className = 'MakeTranslatable' . ucfirst(str_replace('_', '', $model));
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_make_translatable_{$model}.php";
        $filepath = database_path("migrations/{$filename}");

        $stub = $this->getMigrationStub($className, $config['table'], $config['fields']);

        File::put($filepath, $stub);

        $this->line("Created migration: {$filename}");
    }

    /**
     * Obtenir le template de migration
     */
    private function getMigrationStub($className, $table, $fields)
    {
        $fieldsCode = '';
        
        foreach ($fields as $field) {
            $fieldsCode .= "            // Convertir {$field} en JSON pour le multilinguisme\n";
            $fieldsCode .= "            \$table->json('{$field}')->nullable()->change();\n\n";
        }

        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
{$fieldsCode}        });

        // Migrer les données existantes vers le format JSON
        \$this->migrateExistingData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cette migration ne peut pas être facilement inversée
        // car elle convertit les données en JSON
        \$this->comment('Cette migration ne peut pas être inversée automatiquement.');
    }

    /**
     * Migrer les données existantes vers le format multilingue
     */
    private function migrateExistingData(): void
    {
        \$items = DB::table('{$table}')->get();

        foreach (\$items as \$item) {
            \$updates = [];
            
            " . $this->generateDataMigrationCode($fields) . "

            if (!empty(\$updates)) {
                DB::table('{$table}')
                    ->where('id', \$item->id)
                    ->update(\$updates);
            }
        }
    }
};";
    }

    /**
     * Générer le code de migration des données
     */
    private function generateDataMigrationCode($fields)
    {
        $code = '';
        
        foreach ($fields as $field) {
            $code .= "// Migrer {$field}\n";
            $code .= "            if (!\$item->{$field}) {\n";
            $code .= "                \$translation = ['fr' => \$item->{$field}];\n";
            $code .= "                \$updates['{$field}'] = json_encode(\$translation);\n";
            $code .= "            }\n\n            ";
        }

        return $code;
    }
}
