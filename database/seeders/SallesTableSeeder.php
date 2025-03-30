<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SallesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('salles')->insert([
            [
                'nom' => 'Salle 3',
                'capacite' => 10,
                'type' => 'Normale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Salle 4',
                'capacite' => 50,
                'type' => 'VIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Salle 6',
                'capacite' => 20,
                'type' => 'VIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Salle 7',
                'capacite' => 15,
                'type' => 'Normale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Salle 8',
                'capacite' => 50,
                'type' => 'VIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
