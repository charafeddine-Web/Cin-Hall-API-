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
        // Ajout d'une valeur par défaut à la colonne 'role'
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'spectateur'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression de la valeur par défaut de la colonne 'role'
        DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
    }
};
