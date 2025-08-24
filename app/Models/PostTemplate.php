<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTemplate extends Model
{
    protected $fillable = ['page_id', 'post_id', 'private_reply_template', 'public_reply_template', 'is_active'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
