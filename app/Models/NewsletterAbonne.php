<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class NewsletterAbonne extends Model
{
    use HasFactory;

    protected $table = 'newsletter_abonnes';

    protected $fillable = [
        'email',
        'date_inscription',
        'status',
        'token_desabonnement'
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes pour les statuts
    public const STATUS_ACTIF = 'actif';
    public const STATUS_INACTIF = 'inactif';
    public const STATUS_DESABONNE = 'desabonne';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIF => 'Actif',
            self::STATUS_INACTIF => 'Inactif',
            self::STATUS_DESABONNE => 'Désabonné'
        ];
    }

    // Validation rules
    public static function rules()
    {
        return [
            'email' => 'required|email|unique:newsletter_abonnes,email',
            'status' => 'in:' . implode(',', array_keys(self::getStatuses())),
            'token_desabonnement' => 'nullable|string|unique:newsletter_abonnes,token_desabonnement'
        ];
    }

    public static function updateRules($id = null)
    {
        return [
            'email' => 'required|email|unique:newsletter_abonnes,email,' . $id,
            'status' => 'in:' . implode(',', array_keys(self::getStatuses())),
            'token_desabonnement' => 'nullable|string|unique:newsletter_abonnes,token_desabonnement,' . $id
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($abonne) {
            if (empty($abonne->token_desabonnement)) {
                $abonne->token_desabonnement = Str::random(60);
            }
            if (empty($abonne->date_inscription)) {
                $abonne->date_inscription = now();
            }
        });

        static::created(function ($abonne) {
            // Envoyer email de bienvenue
            // event(new NewsletterSubscribed($abonne));
        });

        static::updated(function ($abonne) {
            if ($abonne->isDirty('status') && $abonne->status === self::STATUS_DESABONNE) {
                // event(new NewsletterUnsubscribed($abonne));
            }
        });
    }

    // Scopes
    public function scopeActif(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIF);
    }

    public function scopeInactif(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIF);
    }

    public function scopeDesabonne(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DESABONNE);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('date_inscription', 'desc');
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', 'like', "%{$email}%");
    }

    public function scopeInscritDepuis(Builder $query, $date): Builder
    {
        return $query->where('date_inscription', '>=', $date);
    }

    public function scopeInscritAvant(Builder $query, $date): Builder
    {
        return $query->where('date_inscription', '<=', $date);
    }

    public function scopeAbonnesActifs(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ACTIF]);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIF => 'green',
            self::STATUS_INACTIF => 'yellow',
            self::STATUS_DESABONNE => 'red',
            default => 'gray'
        };
    }

    public function getIsActifAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIF;
    }

    public function getIsInactifAttribute(): bool
    {
        return $this->status === self::STATUS_INACTIF;
    }

    public function getIsDesabonneAttribute(): bool
    {
        return $this->status === self::STATUS_DESABONNE;
    }

    public function getFormattedDateInscriptionAttribute(): string
    {
        return $this->date_inscription->format('d/m/Y à H:i');
    }

    public function getUnsubscribeUrlAttribute(): string
    {
        return route('newsletter.unsubscribe', ['token' => $this->token_desabonnement]);
    }

    public function getDureeAbonnementAttribute(): string
    {
        $diff = $this->date_inscription->diff(now());
        
        if ($diff->y > 0) {
            return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return $diff->m . ' mois';
        } elseif ($diff->d > 0) {
            return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        } else {
            return 'Aujourd\'hui';
        }
    }

    // Mutators
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    // Helper methods
    public function activer(): bool
    {
        $this->status = self::STATUS_ACTIF;
        return $this->save();
    }

    public function desactiver(): bool
    {
        $this->status = self::STATUS_INACTIF;
        return $this->save();
    }

    public function desabonner(): bool
    {
        $this->status = self::STATUS_DESABONNE;
        return $this->save();
    }

    public function reabonner(): bool
    {
        $this->status = self::STATUS_ACTIF;
        return $this->save();
    }

    public function genererNouveauToken(): string
    {
        $this->token_desabonnement = Str::random(60);
        $this->save();
        return $this->token_desabonnement;
    }

    // Méthodes statiques utiles
    public static function countByStatus(): array
    {
        return [
            self::STATUS_ACTIF => static::actif()->count(),
            self::STATUS_INACTIF => static::inactif()->count(),
            self::STATUS_DESABONNE => static::desabonne()->count()
        ];
    }

    public static function getActiveCount(): int
    {
        return static::actif()->count();
    }

    public static function getTotalCount(): int
    {
        return static::count();
    }

    public static function getRecentSubscriptions(int $limit = 10)
    {
        return static::recent()->limit($limit)->get();
    }

    public static function abonnerEmail(string $email): self
    {
        // Vérifier si l'email existe déjà
        $existing = static::where('email', strtolower(trim($email)))->first();
        
        if ($existing) {
            if ($existing->is_desabonne) {
                // Réabonner
                $existing->reabonner();
                return $existing;
            }
            // Déjà abonné
            return $existing;
        }

        // Créer nouvel abonné
        return static::create([
            'email' => strtolower(trim($email)),
            'status' => self::STATUS_ACTIF,
            'date_inscription' => now()
        ]);
    }

    public static function desabonnerParToken(string $token): ?self
    {
        $abonne = static::where('token_desabonnement', $token)->first();
        
        if ($abonne && !$abonne->is_desabonne) {
            $abonne->desabonner();
            return $abonne;
        }
        
        return null;
    }

    public static function findByToken(string $token): ?self
    {
        return static::where('token_desabonnement', $token)->first();
    }

    // Statistiques
    public static function getStatistiques(): array
    {
        $total = static::count();
        $actifs = static::actif()->count();
        $inactifs = static::inactif()->count();
        $desabonnes = static::desabonne()->count();

        $nouveauxCeMois = static::where('date_inscription', '>=', now()->startOfMonth())->count();
        $desabonnesCeMois = static::where('status', self::STATUS_DESABONNE)
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count();

        return [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $inactifs,
            'desabonnes' => $desabonnes,
            'taux_actifs' => $total > 0 ? round(($actifs / $total) * 100, 2) : 0,
            'nouveaux_ce_mois' => $nouveauxCeMois,
            'desabonnes_ce_mois' => $desabonnesCeMois,
            'croissance_mensuelle' => $nouveauxCeMois - $desabonnesCeMois
        ];
    }

    public static function getCroissanceParMois(int $mois = 12): array
    {
        $donnees = [];
        
        for ($i = $mois - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $debut = $date->copy()->startOfMonth();
            $fin = $date->copy()->endOfMonth();
            
            $nouveaux = static::whereBetween('date_inscription', [$debut, $fin])->count();
            $desabonnes = static::where('status', self::STATUS_DESABONNE)
                ->whereBetween('updated_at', [$debut, $fin])
                ->count();
            
            $donnees[] = [
                'mois' => $date->format('M Y'),
                'nouveaux' => $nouveaux,
                'desabonnes' => $desabonnes,
                'net' => $nouveaux - $desabonnes
            ];
        }
        
        return $donnees;
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Vérifications
    public function peutRecevoirNewsletter(): bool
    {
        return $this->is_actif;
    }

    public function estRecentementInscrit(int $jours = 7): bool
    {
        return $this->date_inscription->gt(now()->subDays($jours));
    }
} 