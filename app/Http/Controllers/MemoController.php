<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\EmployeeRank;
use App\Models\Memo;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class MemoController extends Controller
{
    public function create()
    {
        $companies = Company::all();
        return view('memos.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:40960',
            'for_all_companies' => 'nullable|boolean',
            'companies' => 'required_if:for_all_companies,false|array',
            'companies.*' => 'exists:companies,id',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $safeName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('memos', $safeName, 'public');

        $extractedContent = null;
        try{
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $extractedContent = $pdf->getText();
        } catch (\Exception $e) {

        }

        $forAll = $request->boolean('for_all_companies');

        $memo = Memo::create([
            'title' => $validated['title'],
            'file_path' => $path,
            'original_filename' => $originalName,
            'for_all_companies' => $forAll,
            'uploaded_by' => auth()->id(),
            'extracted_content' => $extractedContent,
        ]);

        if (! $forAll) {
            $memo->companies()->attach($validated['companies']);
        }

        return redirect()->route('memos.create')->with('success', 'Memo uploaded successfully.');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Memo::with(['companies', 'categories', 'employeeRanks', 'supersededMemos', 'relatedMemos']);

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

            if($rankId = $request->get('rank_id')){
                $query->where(function ($q) use ($rankId){
                    $q->where('for_all_ranks', true)
                      ->orWhereHas('employeeRanks', fn($q2) => $q2->where('employee_ranks.id', $rankId));
                });
            }

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->where('memo_no', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->whereRaw(
                            "MATCH(title, extracted_content) AGAINST(? IN NATURAL LANGUAGE MODE)",
                            [$search]
                        );
                    }
                })
                ->addColumn('company_list', fn($memo) => $memo->for_all_companies ? 'All' : $memo->companies->pluck('code')->join(', '))
                ->addColumn('rank_list', fn($memo) => $memo->for_all_ranks ? 'All' : $memo->employeeRanks->pluck('name')->join(', '))
                ->addColumn('category_list', function ($memo) {
                    if ($memo->for_all_categories) {
                        return 'All';
                    }

                    $categories = $memo->categories->pluck('name');
                    $full = $categories->join(', ');
                    if ($categories->count() <= 3) {
                        return $full;
                    }

                    return '<span title="'.e($full).'">'.$categories->take(3)->join(', ') . ', +' . ($categories->count() - 3).'</span>';
                })
                ->addColumn('superseded_list', function ($memo) {
                    if ($memo->supersededMemos->isEmpty()) {
                        return '—';
                    }

                    return $memo->supersededMemos->map(function ($m) {
                        return '
                            <button
                                class="memo-pill bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-2 py-1 rounded-full text-xs mr-1 mb-1"
                                data-id="'.$m->id.'"
                                data-memono="'.e($m->memo_no).'"
                                data-title="'.e($m->title).'">

                                <i class="ri-file-text-line mr-1"></i>'.e($m->memo_no).'
                            </button>
                        ';
                    })->implode('');
                })
                ->addColumn('related_list', function ($memo) {
                    if ($memo->relatedMemos->isEmpty()) {
                        return '—';
                    }

                    return $memo->relatedMemos->map(function ($m) {
                        return '
                            <button
                                class="memo-pill bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-2 py-1 rounded-full text-xs mr-1 mb-1"
                                data-id="'.$m->id.'"
                                data-memono="'.e($m->memo_no).'"
                                data-title="'.e($m->title).'">

                                <i class="ri-file-text-line mr-1"></i>'.e($m->memo_no).'
                            </button>
                        ';
                    })->implode('');
                })
                ->addColumn(
                    'created_at_formatted',
                    fn($memo) => $memo->created_at->format('d M Y')
                )
                ->addColumn('actions', function ($memo) {
                    return '
                        <div class="flex items-center gap-3">

                            <button
                                onclick="Livewire.dispatch(\'open-memo-modal-edit\', { memoId: '.$memo->id.' })"
                                class="text-blue-600 hover:text-blue-800"
                                title="Edit">

                                <i class="ri-pencil-line text-lg"></i>

                            </button>

                            <button
                                onclick="archiveMemo('.$memo->id.')"
                                class="text-red-600 hover:text-red-800"
                                title="Archive">

                                <i class="ri-archive-line text-lg"></i>

                            </button>

                        </div>
                    ';
                })
                ->addColumn('memo_no_link', function ($memo) {
                    return '<button type="button" 
                        class="view-pdf-btn text-primary underline" 
                        data-id="'.$memo->id.'" 
                        data-memono="'.e($memo->memo_no).'" 
                        data-title="'.e($memo->title).'">'
                        .e($memo->memo_no).
                    '</button>';
                })
                ->rawColumns(['memo_no_link', 'category_list', 'actions', 'related_list', 'superseded_list'])
                ->make(true);
        }

        $companies = Company::all();
        $categories = Category::all();
        $employeeRanks = EmployeeRank::all();
        
        return view('memos.index', compact('companies', 'categories', 'employeeRanks'));
    }

    public function details(Memo $memo)
    {
        $memo->load(['companies', 'categories', 'supersededMemos', 'relatedMemos', 'supersededMemos']);

        return response()->json([
            'id' => $memo->id,
            'memo_no' => $memo->memo_no,
            'title' => $memo->title,
            'category' => $memo->for_all_categories ? 'All' : ($memo->categories->pluck('name')->join(', ') ?: '—'),
            'company' => $memo->for_all_companies ? 'All' : ($memo->companies->pluck('name')->join(', ') ?: '—'),
            'author' => $memo->author ?: '—',
            'year' => $memo->year,
            'uploaded' => $memo->created_at->format('M d, Y'),
            'related' => $memo->relatedMemos->map(fn($m) => ['id' => $m->id, 'memo_no' => $m->memo_no]),
            'superseded' => $memo->supersededMemos->map(fn($m) => ['id' => $m->id, 'memo_no' => $m->memo_no]),
            'superseded_by' => $memo->supersededByMemos->map(fn($m) => ['id' => $m->id, 'memo_no' => $m->memo_no])
        ]);
    }

    public function searchPicker(Request $request)
    {
        $query = Memo::query();

        if($search = $request->get('query')){
            $query->where('title', 'like', "%{$search}%");
        }

        if($exclude = $request->get('exclude')){
            $query->where('id', '!=', $exclude);
        }

        return $query->select('id', 'title')->limit(20)->get();
    }

    public function archive(Memo $memo)
    {
        $memo->delete();
        return response()->json(['success' => true]);
    }

    public function archived(Request $request)
    {
        if($request->ajax()){
            $query = Memo::onlyTrashed()->with(['companies', 'categories', 'employeeRanks']);

            return DataTables::of($query)
                ->addColumn('company_list', fn($memo) => $memo->for_all_companies ? 'All' : $memo->companies->pluck('code')->join(', '))
                ->addColumn('category_list', fn($memo) => $memo->for_all_categories ? 'All' : $memo->categories->pluck('name')->join(', '))
                ->addColumn('deleted_at_formatted', fn($memo) => $memo->deleted_at->format('M d, Y'))
                ->addColumn('actions', function($memo){
                    return '<button onclick="restoreMemo('.$memo->id.')" class="text-emerald-600 hover:underline">Restore</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        };

        return view('memos.archived');
    }

    public function viewInline(Memo $memo)
    {
        return response()->file(storage_path('app/public/'.$memo->file_path));
    }

    public function download(Memo $memo)
    {
        return response()->download(storage_path('app/public/'.$memo->file_path), $memo->original_filename);
    }

    public function restore($id)
    {
        $memo = Memo::onlyTrashed()->findOrFail($id);
        $memo->restore();
        return response()->json(['success' => true]);
    }

    public function show(Memo $memo)
    {
        return response()->file(storage_path('app/public/'.$memo->file_path));
    }
}
