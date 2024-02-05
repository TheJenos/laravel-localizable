<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory, HasTranslations;

    public $fillable = ['title', 'description'];

    public $translatable = ['title', 'description'];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
