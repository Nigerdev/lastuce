<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
// use Spatie\MediaLibrary\HasMedia;
// use Spatie\MediaLibrary\InteractsWithMedia;
// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AstucesSoumise extends Model // implements HasMedia
{
    use HasFactory; // InteractsWithMedia;

    protected $table = 'astuces_soumises';

    protected $fillable = [
        'nom',
        'email',
        'titre_astuce',
        'categorie',
        'difficulte',
        'temps_estime',
        'description',
        'materiel_requis',
        'etapes',
        'conseils',
        'fichier_joint',
        'images',
        'status',
        'commentaire_admin'
    ];

    protected $casts = [
        'etapes' => 'array',
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes pour les statuts
    public const STATUS_EN_ATTENTE = 'en_attente';
    public const STATUS_APPROUVE = 'approuve';
    public const STATUS_REJETE = 'rejete';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_APPROUVE => 'Approuvé',
            self::STATUS_REJETE => 'Rejeté'
        ];
    }

    // Validation rules
    public static function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'titre_astuce' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'fichier_joint' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:5120', // 5MB max
            'status' => 'in:' . implode(',', array_keys(self::getStatuses())),
            'commentaires_admin' => 'nullable|string'
        ];
    }

    // Scopes
    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EN_ATTENTE);
    }

    public function scopeApprouve(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROUVE);
    }

    public function scopeRejete(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJETE);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('titre_astuce', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_EN_ATTENTE => 'yellow',
            self::STATUS_APPROUVE => 'green',
            self::STATUS_REJETE => 'red',
            default => 'gray'
        };
    }

    public function getIsEnAttenteAttribute(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    public function getIsApprouveAttribute(): bool
    {
        return $this->status === self::STATUS_APPROUVE;
    }

    public function getIsRejeteAttribute(): bool
    {
        return $this->status === self::STATUS_REJETE;
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    // Mutators
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setNomAttribute($value)
    {
        $this->attributes['nom'] = ucwords(strtolower(trim($value)));
    }



    // Helper methods
    public function approuver(string $commentaires = null): bool
    {
        $this->status = self::STATUS_APPROUVE;
        if ($commentaires) {
            $this->commentaires_admin = $commentaires;
        }
        return $this->save();
    }

    public function rejeter(string $commentaires = null): bool
    {
        $this->status = self::STATUS_REJETE;
        if ($commentaires) {
            $this->commentaires_admin = $commentaires;
        }
        return $this->save();
    }

    public function remettrEnAttente(): bool
    {
        $this->status = self::STATUS_EN_ATTENTE;
        $this->commentaires_admin = null;
        return $this->save();
    }

    public function hasAttachments(): bool
    {
        return !empty($this->fichier_joint);
    }

    public function getAttachments()
    {
        return $this->fichier_joint ? [$this->fichier_joint] : [];
    }

    public function getFirstAttachment()
    {
        return $this->fichier_joint;
    }

    // Méthodes statiques utiles
    public static function countByStatus(): array
    {
        return [
            self::STATUS_EN_ATTENTE => static::enAttente()->count(),
            self::STATUS_APPROUVE => static::approuve()->count(),
            self::STATUS_REJETE => static::rejete()->count()
        ];
    }

    public static function getRecentSubmissions(int $limit = 10)
    {
        return static::recent()->limit($limit)->get();
    }

    public static function getPendingCount(): int
    {
        return static::enAttente()->count();
    }

    // Method pour notifier l'admin des nouvelles soumissions
    protected static function boot()
    {
        parent::boot();

        static::created(function ($astuce) {
            // Ici on peut ajouter une notification pour l'admin
            // Par exemple: event(new NewAstuceSubmitted($astuce));
        });

        static::updated(function ($astuce) {
            // Notification quand le statut change
            if ($astuce->isDirty('status')) {
                // event(new AstuceStatusChanged($astuce));
            }
        });
    }
} 