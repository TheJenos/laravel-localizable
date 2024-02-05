<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

trait HasTranslations
{
    protected $translationChanges = [];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (!empty($model->translationChanges)) {
                foreach ($model->translationChanges as $key => $value) {
                    if (!$model->wasChanged($key)) {
                        $model->setParentAttribute($key, $value);
                    }
                }
            }
        });

        static::saved(function (Model $model) {
            $model->saveTranslationChanges();
        });

        static::deleted(function (Model $model) {
            $model->translations()->delete();
        });

        static::addGlobalScope('translation', function ($builder) {
            if (App::isLocale(config('app.fallback_locale'))) return;
            $selectedColumns = $builder->getQuery()->columns;
            $columns = $builder->getModel()->getTranslatableAttributes();
            $diffColumns = empty($selectedColumns) ? $columns : array_intersect($columns, $selectedColumns);
            $builder->with('translations', function ($translationBuilder) use ($diffColumns) {
                $translationBuilder->where('lang', App::getLocale())->whereIn('key', $diffColumns);
            });
        });
    }

    public function saveTranslationChanges()
    {
        if (empty($this->translationChanges)) return;
        foreach ($this->translationChanges as $key => $value) {
            $this->translations()->create([
                'lang' => App::getLocale(),
                'key' => $key,
                'value' => $value,
            ]);
        }
        $this->translationChanges = [];
    }

    public function setParentAttribute($key, $value)
    {
        return parent::setAttribute($key, $value);
    }

    public function setAttribute($key, $value)
    {
        if (!$this->isTranslatableAttribute($key) || App::isLocale(config('app.fallback_locale'))) {
            return parent::setAttribute($key, $value);
        }

        $this->translationChanges[$key] = $value;
    }

    public function getAttribute($key)
    {
        if (!$this->isTranslatableAttribute($key) || App::isLocale(config('app.fallback_locale')) || !$this->relationLoaded('translations')) {
            return parent::getAttribute($key);
        }
        $translation = $this->translations->where('key', $key)->first();
        return $translation ? $translation->value : parent::getAttribute($key);
    }

    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    public function getTranslatableAttributes(): array
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $attribute) {
            if (!isset($array[$attribute])) continue;
            $array[$attribute] = $this->getAttribute($attribute);
        }
        unset($array['translations']);
        return $array;
    }
}
