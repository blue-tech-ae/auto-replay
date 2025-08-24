<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = ['page_id', 'name', 'access_token'];

    public function templates(): HasMany
    {
        return $this->hasMany(PostTemplate::class);
    }
}
