<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
            'الفجر',
            'الضحى',
            'الظهر',
            'العصر',
            'المغرب',
            'العشاء',
            'الوتر',
            'قيام الليل',
            'مساء',
            'صباحا'
        ];

        foreach ($times as $time) {
            DB::table('times')->insert([
                'time' => $time,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
