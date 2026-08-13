<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\EmployeeRank;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['companies', 'departments', 'employeeRank']);

            return DataTables::of($query)
                ->addColumn('role_badge', function ($user) {
                    $color = $user->role === 'admin'
                        ? 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-400 dark:border-indigo-900/50'
                        : 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700';
                    return "<span class='inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {$color}'>" . ucfirst($user->role) . "</span>";
                })
                ->addColumn('company_name', function ($user) {
                    $company = $user->companies->first();
                    return $company ? e($company->code) : '<span class="text-slate-400 dark:text-slate-600">—</span>';
                })
                ->addColumn('department_name', function ($user) {
                    $department = $user->departments->first();
                    return $department ? e($department->name) : '<span class="text-slate-400 dark:text-slate-600">—</span>';
                })
                ->addColumn('rank_name', fn($user) => $user->employeeRank->name ?? '—')
                ->addColumn('actions', function ($user) {
                    return '
                        <div class="flex items-center justify-center gap-2">
                            <button type="button"
                                class="edit-user-btn flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-blue-400 transition-all duration-150 shadow-sm"
                                data-id="' . $user->id . '" title="Edit">
                                <i class="ri-pencil-line text-base"></i>
                            </button>
                            <button type="button"
                                class="delete-user-btn flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-600 hover:bg-red-50 hover:text-red-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-red-950/30 dark:hover:text-red-400 dark:hover:border-red-900/50 transition-all duration-150 shadow-sm"
                                data-id="' . $user->id . '" data-name="' . e($user->name) . '" title="Delete">
                                <i class="ri-delete-bin-line text-base"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['role_badge', 'company_name', 'department_name', 'actions'])
                ->make(true);
        }

        $companies = Company::all();
        $departments = Department::all();
        $employeeRanks = EmployeeRank::all();

        return view('admin.users.index', compact('companies', 'departments', 'employeeRanks'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}