<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'file_path',
        'original_filename',
        'for_all_companies',
        'uploaded_by',
        'extracted_content',
        'memo_no',
        'year',
        'author',
        'for_all_categories',
        'for_all_ranks',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function employeeRanks()
    {
        return $this->belongsToMany(EmployeeRank::class);
    }
}
