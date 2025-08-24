<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_counters', function (Blueprint $t) {
            $t->id();
            $t->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $t->date('day');
            $t->integer('count_day')->default(0);
            $t->string('month');
            $t->integer('count_month')->default(0);
            $t->timestamps();
            $t->unique(['page_id','day','month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_counters');
    }
};
