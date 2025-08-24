<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $t->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $t->date('starts_at');
            $t->date('ends_at')->nullable();
            $t->timestamps();
            $t->unique(['page_id','plan_id','starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_subscriptions');
    }
};
