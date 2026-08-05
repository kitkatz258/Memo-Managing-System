<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\EmployeeRank;
use App\Models\Memo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserMemoController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = Memo::with([
                'companies',
                'categories',
                'employeeRanks',
                'supersededMemos',
                'relatedMemos'
            ]);

            return DataTables::of($query)
                ->filter(function ($query) use ($request){
                    if($search = $request->get('search')['value'] ?? null) {
                        $query->whereRaw(
                            "MATCH(title, extracted_content) AGAINST(? IN NATURAL LANGUAGE MODE)",
                            [$search]
                        );
                    }
                })
                ->addColumn('company_list', fn($memo) =>
                    $memo->for_all_companies
                        ? 'All'
                        : $memo->companies->pluck('code')->join(', ')
                )
                ->addColumn('category_list', fn($memo) =>
                    $memo->for_all_categories
                        ? 'All'
                        : $memo->categories->pluck('name')->join(', ')
                )
                ->addColumn('rank_list', fn($memo) =>
                    $memo->for_all_ranks
                        ? 'All'
                        : $memo->employeeRanks->pluck('name')->join(', ')
                )
                ->addColumn('superseded_list', fn($memo) =>
                    $memo->supersededMemos->pluck('title')->join(', ') ?: '-'
                )
                ->addColumn('related_list', fn($memo) =>
                    $memo->relatedMemos->pluck('title')->join(', ') ?: '-'
                )
                ->addColumn('created_at_formatted', fn($memo) =>
                    $memo->created_at->format('M d, Y')
                )
                ->addColumn('memo_no_link', function($memo){
                    return '<button class="view-pdf-btn text-primary underline"
                        data-id="' . $memo->id . '"
                        data-memono="' . e($memo->memo_no) . '"
                        data-title="' . e($memo->title) . '">'
                        . e($memo->memo_no) .
                    '</button>';
                })
                ->rawColumns(['memo_no_link'])
                ->make(true);
        }

        return view('user.memos.index', [
            'companies' => Company::all(),
            'categories' => Category::all(),
            'employeeRanks' => EmployeeRank::all()
        ]);
    }
}
