<?php

namespace Database\Seeders;

use App\Models\EmployeeRank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = ['RANK AND FILE', 'SUPERVISOR', 'MANAGER'];
        foreach($ranks as $rank) {
            EmployeeRank::create(['name' => $rank]);
        }
    }
}
