<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait Translatable
{

    /**
     * Obtenir le contenu traduit pour un champ donné
     *
     * @param string $field
     * @param string|null $locale
     * @return string
     */
    public function getTranslatedAttribute(string $field, ?string $locale = null): string
    {
        if (!$locale) {
            $locale = App::getLocale();
        }

        // Vérifier si le champ est translatable
        if (!in_array($field, $this->getTranslatableFields())) {
            return $this->getAttribute($field) ?? '';
        }

        // Récupérer la traduction pour la locale courante
        $translation = $this->getTranslation($field, $locale);
        
        // Si pas de traduction, essayer la langue de fallback
        if (empty($translation) && $locale !== config('app.fallback_locale')) {
            $translation = $this->getTranslation($field, config('app.fallback_locale'));
        }

        return $translation ?? $this->getAttribute($field) ?? '';
    }

    /**
     * Récupérer une traduction spécifique
     *
     * @param string $field
     * @param string $locale
     * @return string|null
     */
    protected function getTranslation(string $field, string $locale): ?string
    {
        // Si le champ contient déjà du JSON, le décoder
        $translations = $this->getAttribute($field);
        
        if (is_string($translations)) {
            $translations = json_decode($translations, true);
        }

        if (is_array($translations) && isset($translations[$locale])) {
            return $translations[$locale];
        }

        // Si ce n'est pas un array, retourner la valeur directement pour la locale de fallback
        if ($locale === config('app.fallback_locale') && is_string($translations)) {
            return $translations;
        }

        return null;
    }

    /**
     * Définir une traduction pour un champ
     *
     * @param string $field
     * @param string $value
     * @param string|null $locale
     * @return self
     */
    public function setTranslation(string $field, string $value, ?string $locale = null): self
    {
        if (!$locale) {
            $locale = App::getLocale();
        }

        $translations = $this->getAttribute($field);
        
        if (is_string($translations)) {
            $translations = json_decode($translations, true) ?? [];
        }

        if (!is_array($translations)) {
            $translations = [];
        }

        $translations[$locale] = $value;
        
        $this->setAttribute($field, json_encode($translations));

        return $this;
    }

    /**
     * Obtenir tous les champs translatables
     *
     * @return array
     */
    public function getTranslatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    /**
     * Vérifier si un champ est translatable
     *
     * @param string $field
     * @return bool
     */
    public function isTranslatable(string $field): bool
    {
        return in_array($field, $this->getTranslatableFields());
    }

    /**
     * Obtenir toutes les traductions pour un champ
     *
     * @param string $field
     * @return array
     */
    public function getAllTranslations(string $field): array
    {
        $translations = $this->getAttribute($field);
        
        if (is_string($translations)) {
            return json_decode($translations, true) ?? [];
        }

        return is_array($translations) ? $translations : [];
    }

    /**
     * Vérifier si une traduction existe pour une locale donnée
     *
     * @param string $field
     * @param string $locale
     * @return bool
     */
    public function hasTranslation(string $field, string $locale): bool
    {
        $translations = $this->getAllTranslations($field);
        return isset($translations[$locale]) && !empty($translations[$locale]);
    }

    /**
     * Supprimer une traduction
     *
     * @param string $field
     * @param string $locale
     * @return self
     */
    public function deleteTranslation(string $field, string $locale): self
    {
        $translations = $this->getAllTranslations($field);
        unset($translations[$locale]);
        
        $this->setAttribute($field, json_encode($translations));

        return $this;
    }

    /**
     * Scope pour filtrer par locale
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTranslation($query, string $field, ?string $locale = null)
    {
        if (!$locale) {
            $locale = App::getLocale();
        }

        return $query->whereRaw("JSON_EXTRACT({$field}, '$.{$locale}') IS NOT NULL");
    }

    /**
     * Scope pour rechercher dans les traductions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string $search
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTranslationLike($query, string $field, string $search, ?string $locale = null)
    {
        if (!$locale) {
            $locale = App::getLocale();
        }

        return $query->whereRaw("JSON_EXTRACT({$field}, '$.{$locale}') LIKE ?", ["%{$search}%"]);
    }

    /**
     * Accesseur magique pour les champs translatables
     *
     * @param string $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        // Si c'est un champ translatable, retourner la traduction
        if ($this->isTranslatable($key)) {
            return $this->getTranslatedAttribute($key);
        }

        return parent::getAttributeValue($key);
    }

    /**
     * Mutateur magique pour les champs translatables
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // Si c'est un champ translatable et qu'on reçoit un array de traductions
        if ($this->isTranslatable($key) && is_array($value)) {
            $value = json_encode($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Convertir les champs translatables en array
     *
     * @return array
     */
    public function translatableToArray(): array
    {
        $array = [];
        
        foreach ($this->getTranslatableFields() as $field) {
            $array[$field] = $this->getAllTranslations($field);
        }

        return $array;
    }

    /**
     * Obtenir le contenu pour une locale spécifique
     *
     * @param string $locale
     * @return array
     */
    public function getTranslatedContent(string $locale): array
    {
        $content = $this->toArray();
        
        foreach ($this->getTranslatableFields() as $field) {
            $content[$field] = $this->getTranslatedAttribute($field, $locale);
        }

        return $content;
    }

    /**
     * Sauvegarder les traductions
     *
     * @param array $translations
     * @return self
     */
    public function saveTranslations(array $translations): self
    {
        foreach ($translations as $field => $localeTranslations) {
            if ($this->isTranslatable($field) && is_array($localeTranslations)) {
                foreach ($localeTranslations as $locale => $value) {
                    $this->setTranslation($field, $value, $locale);
                }
            }
        }

        return $this;
    }
} 