<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wordcloud_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visualization_id')->constrained('visualizations')->onDelete('cascade');
            $table->string('word');
            $table->integer('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wordcloud_data');
    }
};