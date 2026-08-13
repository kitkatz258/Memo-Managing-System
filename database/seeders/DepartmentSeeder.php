<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = ['HR', 'SALES', 'PID', 'SERVICE', 'ENGR.', 'ACCTG.', 'CMG', 'C&C', 'TREASURY', 'MARKETING/ADVERTISING', 'WAREHOUSE', 'PURCHASING', 'PAYROLL', 'FAAP', 'IT', 'AUDIT', 'FACILITIES'];
        foreach($departments as $dept){
            Department::updateOrCreate(['name' => $dept]);
        }
    }
}
