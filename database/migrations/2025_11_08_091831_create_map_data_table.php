<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visualization_id')->constrained('visualizations')->onDelete('cascade');
            $table->string('region_id');
            $table->string('region_name');
            $table->string('variable_name');
            $table->float('variable_value');
            $table->timestamps();
            
            $table->unique(['visualization_id', 'region_id', 'variable_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_data');
    }
};