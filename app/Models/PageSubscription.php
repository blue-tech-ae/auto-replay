<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSubscription extends Model
{
    protected $fillable = ['page_id','plan_id','starts_at','ends_at'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
