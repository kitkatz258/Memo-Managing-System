<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => 'Multi-Line Building Systems Inc.', 'code' => 'MBI'],
            ['name' => 'Multi-Line Structures Corp.', 'code' => 'MSC'],
            ['name' => 'Filipinas Multi-Line Corp.', 'code' => 'FMC'],
            ['name' => 'WorldCraft Furniture', 'code' => 'WC']
        ];

        foreach($companies as $company) {
            Company::create($company);
        }
    }
}
