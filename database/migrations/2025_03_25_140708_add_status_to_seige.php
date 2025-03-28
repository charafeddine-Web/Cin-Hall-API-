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
        Schema::table('sieges', function (Blueprint $table) {
            $table->enum('status', ['available', 'reserved', 'occupied'])
                ->after('type')
                ->default('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sieges', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
