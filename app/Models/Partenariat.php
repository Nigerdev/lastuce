<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Partenariat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_entreprise',
        'contact',
        'email',
        'message',
        'status',
        'notes_internes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes pour les statuts
    public const STATUS_NOUVEAU = 'nouveau';
    public const STATUS_EN_COURS = 'en_cours';
    public const STATUS_ACCEPTE = 'accepte';
    public const STATUS_REFUSE = 'refuse';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NOUVEAU => 'Nouveau',
            self::STATUS_EN_COURS => 'En cours',
            self::STATUS_ACCEPTE => 'Accepté',
            self::STATUS_REFUSE => 'Refusé'
        ];
    }

    // Validation rules
    public static function rules()
    {
        return [
            'nom_entreprise' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:20',
            'status' => 'in:' . implode(',', array_keys(self::getStatuses())),
            'notes_internes' => 'nullable|string'
        ];
    }

    // Scopes
    public function scopeNouveau(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_NOUVEAU);
    }

    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EN_COURS);
    }

    public function scopeAccepte(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACCEPTE);
    }

    public function scopeRefuse(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REFUSE);
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

    public function scopeByEntreprise(Builder $query, string $entreprise): Builder
    {
        return $query->where('nom_entreprise', 'like', "%{$entreprise}%");
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom_entreprise', 'like', "%{$search}%")
              ->orWhere('contact', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_NOUVEAU, self::STATUS_EN_COURS]);
    }

    public function scopeTraite(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ACCEPTE, self::STATUS_REFUSE]);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NOUVEAU => 'blue',
            self::STATUS_EN_COURS => 'yellow',
            self::STATUS_ACCEPTE => 'green',
            self::STATUS_REFUSE => 'red',
            default => 'gray'
        };
    }

    public function getIsNouveauAttribute(): bool
    {
        return $this->status === self::STATUS_NOUVEAU;
    }

    public function getIsEnCoursAttribute(): bool
    {
        return $this->status === self::STATUS_EN_COURS;
    }

    public function getIsAccepteAttribute(): bool
    {
        return $this->status === self::STATUS_ACCEPTE;
    }

    public function getIsRefuseAttribute(): bool
    {
        return $this->status === self::STATUS_REFUSE;
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_NOUVEAU, self::STATUS_EN_COURS]);
    }

    public function getIsTraiteAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTE, self::STATUS_REFUSE]);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    public function getShortMessageAttribute(): string
    {
        return \Str::limit($this->message, 100);
    }

    // Mutators
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setNomEntrepriseAttribute($value)
    {
        $this->attributes['nom_entreprise'] = trim($value);
    }

    public function setContactAttribute($value)
    {
        $this->attributes['contact'] = trim($value);
    }

    // Helper methods
    public function marquerEnCours(string $notes = null): bool
    {
        $this->status = self::STATUS_EN_COURS;
        if ($notes) {
            $this->notes_internes = $notes;
        }
        return $this->save();
    }

    public function accepter(string $notes = null): bool
    {
        $this->status = self::STATUS_ACCEPTE;
        if ($notes) {
            $this->notes_internes = $notes;
        }
        return $this->save();
    }

    public function refuser(string $notes = null): bool
    {
        $this->status = self::STATUS_REFUSE;
        if ($notes) {
            $this->notes_internes = $notes;
        }
        return $this->save();
    }

    public function remettrNouveau(): bool
    {
        $this->status = self::STATUS_NOUVEAU;
        return $this->save();
    }

    public function ajouterNotes(string $notes): bool
    {
        $existingNotes = $this->notes_internes ? $this->notes_internes . "\n\n" : '';
        $this->notes_internes = $existingNotes . "[" . now()->format('d/m/Y H:i') . "] " . $notes;
        return $this->save();
    }

    // Méthodes statiques utiles
    public static function countByStatus(): array
    {
        return [
            self::STATUS_NOUVEAU => static::nouveau()->count(),
            self::STATUS_EN_COURS => static::enCours()->count(),
            self::STATUS_ACCEPTE => static::accepte()->count(),
            self::STATUS_REFUSE => static::refuse()->count()
        ];
    }

    public static function getRecentDemandes(int $limit = 10)
    {
        return static::recent()->limit($limit)->get();
    }

    public static function getNewCount(): int
    {
        return static::nouveau()->count();
    }

    public static function getActiveCount(): int
    {
        return static::active()->count();
    }

    // Relations potentielles
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($partenariat) {
            // Notification admin nouvelle demande
            // event(new NewPartnershipRequest($partenariat));
        });

        static::updated(function ($partenariat) {
            if ($partenariat->isDirty('status')) {
                // Notification changement de statut
                // event(new PartnershipStatusChanged($partenariat));
            }
        });
    }

    // Méthodes pour l'historique
    public function getStatusHistory(): array
    {
        // Simulation d'un historique - dans une vraie app, on aurait une table dédiée
        return [
            [
                'status' => $this->status,
                'date' => $this->updated_at,
                'notes' => $this->notes_internes
            ]
        ];
    }

    // Méthodes de recherche avancée
    public static function searchAdvanced(array $filters)
    {
        $query = static::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['entreprise'])) {
            $query->where('nom_entreprise', 'like', "%{$filters['entreprise']}%");
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', "%{$filters['email']}%");
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->recent();
    }
} 