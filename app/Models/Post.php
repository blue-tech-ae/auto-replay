<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    protected $fillable = ['page_id','post_id','permalink_url','title','is_active'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function template(): HasOne
    {
        return $this->hasOne(PostTemplate::class, 'post_id', 'post_id');
    }
}
