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
        if ($request->ajax()) {
            $query = Memo::with(['companies', 'categories', 'supersededMemos', 'relatedMemos']);

            if ($companyId = $request->get('company_id')) {
                $query->where(function ($q) use ($companyId) {
                    $q->where('for_all_companies', true)
                    ->orWhereHas('companies', fn($q2) => $q2->where('companies.id', $companyId));
                });
            }

            if($categoryId = $request->get('category_id')){
                $query->where(function ($q) use ($categoryId){
                    $q->where('for_all_categories', true)
                      ->orWhereHas('categories', fn($q2) => $q2->where('categories.id', $categoryId));
                });
            }

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    $search = trim($request->input('search.value', ''));

                    if ($search === '') {
                        return;
                    }

                    $query->where(function ($q) use ($search) {
                        $q->where('memo_no', 'LIKE', "%{$search}%")
                        ->orWhere('title', 'LIKE', "%{$search}%")
                        ->orWhere('author', 'LIKE', "%{$search}%")
                        ->orWhere('year', 'LIKE', "%{$search}%")
                        ->orWhereRaw(
                            "MATCH(title, extracted_content) AGAINST(? IN NATURAL LANGUAGE MODE)",
                            [$search]
                        );
                    });
                })
                ->addColumn('company_list', function($memo) {
                    if ($memo->for_all_companies) {
                        return "<span class='inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 px-2.5 py-0.5 text-xs font-medium'>All</span>";
                    }
                    
                    $pills = $memo->companies->pluck('code')
                        ->map(fn($code) => "<span class='inline-flex items-center rounded-full bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-2.5 py-0.5 text-xs font-semibold'>" . e($code) . "</span>")
                        ->join('');
                    return "<div class='flex flex-wrap gap-1.5 max-w-[160px]'>{$pills}</div>";
                })
                ->addColumn('category_list', function ($memo) {
                    if ($memo->for_all_categories) {
                        return "<span class='inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 px-2.5 py-0.5 text-xs font-medium'>All</span>";
                    }

                    $categories = $memo->categories->pluck('name');
                    $total = $categories->count();

                    $pills = $categories->take(2)->map(fn($name) => 
                        "<span class='inline-flex items-center rounded-full bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap'>" . e($name) . "</span>"
                    )->join('');

                    if ($total > 2) {
                        $remaining = $total - 2;
                        $fullList = e($categories->join(', '));
                        $pills .= "<span title='{$fullList}' class='cursor-pointer inline-flex items-center rounded-full bg-slate-200 text-slate-600 border border-slate-300 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800 px-2 py-0.5 text-xs font-medium'>+{$remaining}</span>";
                    }

                    return "<div class='flex flex-wrap gap-1.5 max-w-[220px]'>{$pills}</div>";
                })
                ->addColumn('superseded_list', function ($memo) {
                    if ($memo->supersededMemos->isEmpty()) {
                        return "<span class='text-slate-400 dark:text-slate-600'>—</span>";
                    }
                    return $memo->supersededMemos->map(function ($m) {
                        return '
                            <button class="memo-pill bg-slate-100 text-slate-700 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400 px-2.5 py-1 rounded-full text-xs mr-1 mb-1 transition-all duration-150 inline-flex items-center font-medium" data-id="'.$m->id.'" data-memono="'.e($m->memo_no).'" data-title="'.e($m->title).'">
                                <i class="ri-file-text-line mr-1 text-[11px]"></i>'.e($m->memo_no).'
                            </button>
                        ';
                    })->implode('');
                })
                ->addColumn('related_list', function ($memo) {
                    if ($memo->relatedMemos->isEmpty()) {
                        return "<span class='text-slate-400 dark:text-slate-600'>—</span>";
                    }
                    return $memo->relatedMemos->map(function ($m) {
                        return '
                            <button class="memo-pill bg-slate-100 text-slate-700 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400 px-2.5 py-1 rounded-full text-xs mr-1 mb-1 transition-all duration-150 inline-flex items-center font-medium" data-id="'.$m->id.'" data-memono="'.e($m->memo_no).'" data-title="'.e($m->title).'">
                                <i class="ri-file-text-line mr-1 text-[11px]"></i>'.e($m->memo_no).'
                            </button>
                        ';
                    })->implode('');
                })
                ->addColumn('memo_no_link', function ($memo) {
                    return '
                    <button type="button" class="view-pdf-btn memo-pill inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400 font-semibold text-xs tracking-wider transition-all duration-150" data-id="'.$memo->id.'" data-memono="'.e($memo->memo_no).'" data-title="'.e($memo->title).'">
                        <i class="ri-file-text-line text-xs"></i>
                        <span>'.e($memo->memo_no).'</span>
                    </button>
                    ';
                })
                ->addColumn('title_with_preview', function ($memo) use ($request) {
                    $search = trim($request->get('search')['value'] ?? '');

                    if ($search === '') {
                        return '
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                ' . e($memo->title) . '
                            </div>
                        ';
                    }

                    $content = strip_tags($memo->extracted_content ?? '');
                    $content = preg_replace('/\s+/', ' ', $content);
                    $content = trim($content);

                    $searchWords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);

                    $position = false;
                    $matchedWord = null;

                    foreach ($searchWords as $word) {
                        $word = trim($word);

                        if ($word === '') {
                            continue;
                        }

                        $foundPosition = mb_stripos($content, $word);

                        if ($foundPosition !== false) {
                            if ($position === false || $foundPosition < $position) {
                                $position = $foundPosition;
                                $matchedWord = $word;
                            }
                        }
                    }

                    if ($position === false || $matchedWord === null) {
                        return '
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                ' . e($memo->title) . '
                            </div>
                        ';
                    }

                    $start = max(0, $position - 80);
                    $length = 220;

                    $snippet = mb_substr($content, $start, $length);

                    if ($start > 0) {
                        $snippet = '...' . $snippet;
                    }

                    if (($start + $length) < mb_strlen($content)) {
                        $snippet .= '...';
                    }

                    $snippet = e($snippet);

                    $snippet = preg_replace(
                        '/' . preg_quote(e($matchedWord), '/') . '/iu',
                        '<mark class="bg-yellow-200 dark:bg-yellow-500/30 text-slate-900 dark:text-yellow-200 rounded px-0.5">$0</mark>',
                        $snippet
                    );

                    return '
                        <div class="min-w-[260px] max-w-[420px]">
                            <div class="font-medium text-slate-800 dark:text-slate-200">
                                ' . e($memo->title) . '
                            </div>

                            <div class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400 line-clamp-2">
                                ' . $snippet . '
                            </div>
                        </div>
                    ';
                })
                ->rawColumns(['memo_no_link', 'title_with_preview', 'company_list', 'rank_list', 'category_list', 'actions', 'related_list', 'superseded_list'])
                ->make(true);
        }

        $companies = Company::all();
        $categories = Category::all();
        $employeeRanks = EmployeeRank::all();
        
        return view('user.memos.index', compact('companies', 'categories', 'employeeRanks'));
    }
}
