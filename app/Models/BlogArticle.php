<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
// use Spatie\MediaLibrary\HasMedia;
// use Spatie\MediaLibrary\InteractsWithMedia;
// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BlogArticle extends Model // implements HasMedia
{
    use HasFactory; // InteractsWithMedia;

    protected $fillable = [
        'titre',
        'contenu',
        'image',
        'slug',
        'date_publication',
        'is_published',
        'extrait',
        'meta_description'
    ];

    protected $casts = [
        'date_publication' => 'datetime',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'date_publication'
    ];

    // Boot method pour générer automatiquement le slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->titre);
            }
            
            if (empty($article->extrait) && $article->contenu) {
                $article->extrait = Str::limit(strip_tags($article->contenu), 160);
            }
            
            if (empty($article->meta_description) && $article->extrait) {
                $article->meta_description = Str::limit($article->extrait, 160);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('titre') && empty($article->getOriginal('slug'))) {
                $article->slug = Str::slug($article->titre);
            }
            
            if ($article->isDirty('contenu') && empty($article->extrait)) {
                $article->extrait = Str::limit(strip_tags($article->contenu), 160);
            }
        });
    }

    // Validation rules
    public static function rules()
    {
        return [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string|min:50',
            'image' => 'nullable|string',
            'slug' => 'nullable|string|unique:blog_articles,slug',
            'date_publication' => 'nullable|date',
            'is_published' => 'boolean',
            'extrait' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:160'
        ];
    }

    public static function updateRules($id = null)
    {
        return [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string|min:50',
            'image' => 'nullable|string',
            'slug' => 'nullable|string|unique:blog_articles,slug,' . $id,
            'date_publication' => 'nullable|date',
            'is_published' => 'boolean',
            'extrait' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:160'
        ];
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('is_published', false);
    }

    public function scopePublishedAndVisible(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('date_publication')
                  ->orWhere('date_publication', '<=', now());
            });
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('date_publication')
            ->where('date_publication', '>', now());
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('date_publication', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function scopeOlder(Builder $query): Builder
    {
        return $query->orderBy('date_publication', 'asc')
            ->orderBy('created_at', 'asc');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('titre', 'like', "%{$search}%")
              ->orWhere('contenu', 'like', "%{$search}%")
              ->orWhere('extrait', 'like', "%{$search}%");
        });
    }

    public function scopeByMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('date_publication', $year)
            ->whereMonth('date_publication', $month);
    }

    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->whereYear('date_publication', $year);
    }

    // Accessors
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getIsVisibleAttribute(): bool
    {
        if (!$this->is_published) {
            return false;
        }
        
        return !$this->date_publication || $this->date_publication->isPast();
    }

    public function getIsScheduledAttribute(): bool
    {
        return $this->is_published && $this->date_publication && $this->date_publication->isFuture();
    }

    public function getIsDraftAttribute(): bool
    {
        return !$this->is_published;
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_published) {
            return 'Brouillon';
        }
        
        if ($this->is_scheduled) {
            return 'Programmé';
        }
        
        return 'Publié';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Brouillon' => 'gray',
            'Programmé' => 'yellow',
            'Publié' => 'green',
            default => 'gray'
        };
    }

    public function getFormattedDatePublicationAttribute(): ?string
    {
        return $this->date_publication?->format('d/m/Y à H:i');
    }

    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->contenu));
        return max(1, ceil($wordCount / 200)); // 200 mots par minute
    }

    public function getWordCountAttribute(): int
    {
        return str_word_count(strip_tags($this->contenu));
    }

    public function getShortExtraitAttribute(): string
    {
        return Str::limit($this->extrait ?: strip_tags($this->contenu), 100);
    }

    public function getUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    // Mutators
    public function setTitreAttribute($value)
    {
        $this->attributes['titre'] = trim($value);
        
        // Générer le slug si pas encore défini
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function setContenuAttribute($value)
    {
        $this->attributes['contenu'] = $value;
        
        // Générer l'extrait automatiquement si pas défini
        if (empty($this->attributes['extrait'])) {
            $this->attributes['extrait'] = Str::limit(strip_tags($value), 160);
        }
    }

    public function setExtraitAttribute($value)
    {
        $this->attributes['extrait'] = $value;
        
        // Générer la meta description si pas définie
        if (empty($this->attributes['meta_description'])) {
            $this->attributes['meta_description'] = Str::limit($value ?: strip_tags($this->contenu), 160);
        }
    }



    // Helper methods
    public function publier(?\DateTime $datePublication = null): bool
    {
        $this->is_published = true;
        $this->date_publication = $datePublication ?: now();
        return $this->save();
    }

    public function depublier(): bool
    {
        $this->is_published = false;
        return $this->save();
    }

    public function programmer(\DateTime $datePublication): bool
    {
        $this->is_published = true;
        $this->date_publication = $datePublication;
        return $this->save();
    }

    public function hasFeaturedImage(): bool
    {
        return !empty($this->image);
    }

    public function getFeaturedImage(?string $conversion = null)
    {
        return $this->image;
    }

    public function getFeaturedImageMedia()
    {
        return $this->image;
    }

    // Méthodes statiques utiles
    public static function getPublishedCount(): int
    {
        return static::published()->count();
    }

    public static function getDraftCount(): int
    {
        return static::draft()->count();
    }

    public static function getScheduledCount(): int
    {
        return static::scheduled()->count();
    }

    public static function getRecentArticles(int $limit = 5)
    {
        return static::publishedAndVisible()->recent()->limit($limit)->get();
    }

    public static function getPopularArticles(int $limit = 5)
    {
        // Pour l'instant retourne les plus récents, mais on pourrait ajouter un système de vues
        return static::getRecentArticles($limit);
    }

    public static function getArchives(): array
    {
        $archives = static::published()
            ->selectRaw('YEAR(date_publication) as year, MONTH(date_publication) as month, COUNT(*) as count')
            ->whereNotNull('date_publication')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return $archives->map(function ($archive) {
            return [
                'year' => $archive->year,
                'month' => $archive->month,
                'count' => $archive->count,
                'label' => \Carbon\Carbon::createFromDate($archive->year, $archive->month, 1)->format('F Y'),
                'url' => route('blog.archives', ['year' => $archive->year, 'month' => $archive->month])
            ];
        })->toArray();
    }

    // Recherche avancée
    public static function searchAdvanced(array $filters)
    {
        $query = static::query();

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'published') {
                $query->published();
            } elseif ($filters['status'] === 'draft') {
                $query->draft();
            } elseif ($filters['status'] === 'scheduled') {
                $query->scheduled();
            }
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['year'])) {
            $query->byYear($filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->byMonth($filters['year'] ?? date('Y'), $filters['month']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_publication', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date_publication', '<=', $filters['date_to']);
        }

        return $query->recent();
    }

    // SEO helpers
    public function getSeoTitle(): string
    {
        return $this->titre . ' - L\'Astuce Blog';
    }

    public function getSeoDescription(): string
    {
        return $this->meta_description ?: $this->extrait ?: Str::limit(strip_tags($this->contenu), 160);
    }

    public function getSeoKeywords(): array
    {
        // Extraction simple de mots-clés - pourrait être améliorée
        $text = strtolower($this->titre . ' ' . strip_tags($this->contenu));
        $words = preg_split('/\s+/', $text);
        $stopWords = ['le', 'la', 'les', 'de', 'du', 'des', 'et', 'ou', 'un', 'une'];
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        return array_slice(array_unique($keywords), 0, 10);
    }
} 