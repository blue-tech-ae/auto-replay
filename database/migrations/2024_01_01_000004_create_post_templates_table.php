<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_templates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $t->string('post_id');
            $t->text('private_reply_template');
            $t->text('public_reply_template')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['page_id','post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_templates');
    }
};
