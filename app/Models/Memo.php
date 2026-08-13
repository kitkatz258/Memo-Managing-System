<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        'for_all_departments',
        'for_all_ranks',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_memo');
    }

    public function employeeRanks()
    {
        return $this->belongsToMany(EmployeeRank::class);
    }

    public function supersededMemos()
    {
        return $this->belongsToMany(Memo::class, 'memo_supersessions', 'memo_id', 'superseded_memo_id');
    }

    public function relatedMemos()
    {
        return $this->belongsToMany(Memo::class, 'memo_relations', 'memo_id', 'related_memo_id');
    }

    public function supersededByMemos()
    {
        return $this->belongsToMany(Memo::class, 'memo_supersessions', 'superseded_memo_id', 'memo_id');
    }

    public function log()
    {
        return $this->hasMany(MemoLog::class)->latest();
    }

    public function isViewableBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $userCompanyIds = $user->companies->pluck('id');
        $companyOk = $this->for_all_companies
            || $this->companies->pluck('id')->intersect($userCompanyIds)->isNotEmpty();

        $userDepartmentIds = $user->departments->pluck('id');
        $departmentOk = $this->for_all_departments
            || $this->departments->pluck('id')->intersect($userDepartmentIds)->isNotEmpty();

        $rankOk = $this->for_all_ranks
            || ($user->employee_rank_id && $this->employeeRanks->contains('id', $user->employee_rank_id));

        return $companyOk && $departmentOk && $rankOk;
    }
}