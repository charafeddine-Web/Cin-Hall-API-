<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('salles', function (Blueprint $table) {
            $table->integer('capacite')->default(0); // Ajout de la colonne 'capacite'
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('salles', function (Blueprint $table) {
            $table->dropColumn('capacite'); // Suppression de la colonne 'capacite'
        });
    }

};
