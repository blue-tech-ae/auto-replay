<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_logs', function (Blueprint $t) {
            $t->id();
            $t->string('comment_id')->unique();
            $t->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $t->string('post_id');
            $t->enum('status', ['sent','failed','skipped'])->default('sent');
            $t->integer('error_code')->nullable();
            $t->text('error_message')->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_logs');
    }
};
