<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['HR', 'SALES', 'PID', 'SERVICE', 'ENGR.', 'ACCTG.', 'CMG', 'C&C', 'TREASURY', 'MARKETING/ADVERTISING', 'WAREHOUSE', 'PURCHASING', 'PAYROLL', 'FAAP', 'IT', 'AUDIT', 'FACILITIES'];
        foreach($categories as $cat) {
            \App\Models\Category::create(['name' => $cat]);
        }
    }
}
