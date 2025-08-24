<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageCounter extends Model
{
    protected $fillable = ['page_id','day','count_day','month','count_month'];
}
