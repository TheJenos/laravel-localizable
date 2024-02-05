<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory, HasTranslations;

    public $fillable = ['comment1', 'comment2'];

    public $translatable = ['comment1', 'comment2'];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}
