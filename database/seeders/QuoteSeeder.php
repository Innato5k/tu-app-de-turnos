<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = [
            ['text' => 'La tecnología es mejor cuando une a las personas.', 'author' => 'Matt Mullenweg'],
            ['text' => 'Tu mente es tu mejor activo, cuidala tanto como a tu código.', 'author' => 'Analista de Sistemas'],
            ['text' => 'El éxito es la suma de pequeños esfuerzos repetidos día tras día.', 'author' => 'Robert Collier'],
            ['text' => 'No cuentes los días, haz que los días cuenten.', 'author' => 'Muhammad Ali'],
        ];

        foreach ($quotes as $quote) {
            \App\Models\Quote::create($quote);
        }
    }
}
