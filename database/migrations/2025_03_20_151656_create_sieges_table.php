<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sieges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salle_id')->constrained()->onDelete('cascade'); // Relation avec la salle
            $table->string('numero'); // Numéro du siège (ex: A1, B2)
            $table->enum('type', ['standard', 'couple'])->default('standard'); // Type de siège
            $table->boolean('reserve')->default(false); // Indique si le siège est réservé ou non
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sieges');
    }
};
