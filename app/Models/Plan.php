<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name','max_active_posts','daily_private_replies','monthly_private_replies'];
}
