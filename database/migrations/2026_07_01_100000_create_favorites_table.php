<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('external_id');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Fast lookup for a specific favorite
            $table->unique(['user_id', 'type', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
