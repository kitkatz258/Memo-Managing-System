<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use App\Models\EmployeeRank;
use App\Models\Memo;
use Dflydev\DotAccessData\Data;
use App\Models\MemoLog;
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
            $extractedContent = $this->cleanExtractedText($extractedContent);
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

        return redirect()->route('dashboard')->with('success', 'Memo uploaded successfully.');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Memo::with(['companies', 'departments', 'employeeRanks', 'supersededMemos', 'relatedMemos']);

            if ($companyId = $request->get('company_id')) {
                $query->where(function ($q) use ($companyId) {
                    $q->where('for_all_companies', true)
                    ->orWhereHas('companies', fn($q2) => $q2->where('companies.id', $companyId));
                });
            }

            if($departmentId = $request->get('department_id')){
                $query->where(function ($q) use ($departmentId){
                    $q->where('for_all_departments', true)
                      ->orWhereHas('departments', fn($q2) => $q2->where('departments.id', $departmentId));
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
                ->addColumn('rank_list', function($memo) {
                    if ($memo->for_all_ranks) {
                        return "<span class='inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 px-2.5 py-0.5 text-xs font-medium'>All</span>";
                    }

                    $pills = $memo->employeeRanks->pluck('name')
                        ->map(fn($name) => "<span class='inline-flex items-center rounded-full bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap'>" . e($name) . "</span>")
                        ->join('');
                        
                    return "<div class='flex flex-wrap gap-1.5 max-w-[200px]'>{$pills}</div>";
                })
                ->addColumn('department_list', function ($memo) {
                    static $totalDepartmentCount = null;
                    if ($totalDepartmentCount === null) {
                        $totalDepartmentCount = Department::count();
                    }

                    $departments = $memo->departments->pluck('name');
                    $total = $departments->count();

                    if ($memo->for_all_departments || ($totalDepartmentCount > 0 && $total === $totalDepartmentCount)) {
                        return "<span class='inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 px-2.5 py-0.5 text-xs font-medium'>All</span>";
                    }

                    $pills = $departments->take(2)->map(fn($name) => 
                        "<span class='inline-flex items-center rounded-full bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap'>" . e($name) . "</span>"
                    )->join('');

                    if ($total > 2) {
                        $remaining = $total - 2;
                        $fullList = e($departments->join(', '));
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
                ->addColumn('actions', function ($memo) {
                    return '
                    <div class="flex items-center gap-2">
                        <button onclick="Livewire.dispatch(\'open-memo-modal-edit\', { memoId: '.$memo->id.' })" 
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-blue-400 transition-all duration-150 shadow-sm" 
                                title="Edit">
                            <i class="ri-pencil-line text-base"></i>
                        </button>
                        <button onclick="archiveMemo('.$memo->id.')" 
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-600 hover:bg-red-50 hover:text-red-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-red-950/30 dark:hover:text-red-400 dark:hover:border-red-900/50 transition-all duration-150 shadow-sm" 
                                title="Archive">
                            <i class="ri-archive-line text-base"></i>
                        </button>
                        <button onclick="openHistoryModal('.$memo->id.')"
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-indigo-950/30 dark:hover:text-indigo-400 transition-all duration-150 shadow-sm"
                                title="History">
                            <i class="ri-history-line text-base"></i>
                        </button>
                    </div>
                    ';
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
                    $search = trim($request->input('search.value', ''));

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

                    $searchWords = preg_split(
                        '/\s+/',
                        $search,
                        -1,
                        PREG_SPLIT_NO_EMPTY
                    );

                    $position = false;

                    foreach ($searchWords as $word) {
                        $word = trim($word);

                        if ($word === '') {
                            continue;
                        }

                        $foundPosition = mb_stripos($content, $word);

                        if ($foundPosition !== false) {
                            if ($position === false || $foundPosition < $position) {
                                $position = $foundPosition;
                            }
                        }
                    }

                    if ($position === false) {
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

                    foreach ($searchWords as $word) {
                        $word = trim($word);

                        if ($word === '') {
                            continue;
                        }

                        $escapedWord = e($word);

                        $snippet = preg_replace(
                            '/' . preg_quote($escapedWord, '/') . '/iu',
                            '<mark class="bg-yellow-200 dark:bg-yellow-500/30 text-slate-900 dark:text-yellow-200 rounded px-0.5">$0</mark>',
                            $snippet
                        );
                    }

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
                ->rawColumns(['memo_no_link', 'title_with_preview', 'company_list', 'rank_list', 'department_list', 'actions', 'related_list', 'superseded_list'])
                ->make(true);
        }

        $companies = Company::all();
        $departments = Department::all();
        $employeeRanks = EmployeeRank::all();
        
        return view('memos.index', compact('companies', 'departments', 'employeeRanks'));
    }

    public function details(Memo $memo)
    {
        $memo = Memo::withTrashed()
        ->with([
            'companies',
            'departments',
            'employeeRanks',
            'supersededMemos',
            'relatedMemos',
            'supersededByMemos'
        ])
        ->findOrFail($memo->id);

        if (! $memo->isViewableBy(auth()->user())) {
            return response()->json([
                'message' => 'You do not have access to view this memo based on your assigned company, department, or rank.',
            ], 403);
        }

        MemoLog::create([
            'memo_id' => $memo->id,
            'user_id' => auth()->id(),
            'action' => 'viewed',
        ]);

        return response()->json([
            'id' => $memo->id,
            'memo_no' => $memo->memo_no,
            'title' => $memo->title,
            'departments' => $memo->for_all_departments ? 'All' : ($memo->departments->pluck('name')->join(', ') ?: '—'),
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
            $query->where('memo_no', 'like', "%{$search}%");
        }

        if($exclude = $request->get('exclude')){
            $query->where('id', '!=', $exclude);
        }

        return $query->select('id', 'title', 'memo_no')->limit(20)->get();
    }

    public function archive(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $memo = Memo::findOrFail($id);
        $memo->delete();

        MemoLog::create([
            'memo_id' => $memo->id,
            'user_id' => auth()->id(),
            'action' => 'archived',
            'remarks' => $request->remarks,
        ]);

        return response()->json(['success' => true]);
    }

    public function archived(Request $request)
    {
        if($request->ajax()){
            $query = Memo::onlyTrashed()->with(['companies', 'departments', 'employeeRanks']);
            
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
                ->addColumn('company_list', function ($memo) {
                    if ($memo->for_all_companies) {
                        return "
                        <span class='inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200
                        dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50
                        px-2.5 py-0.5 text-xs font-medium'>
                            All
                        </span>";
                    }

                    return $memo->companies
                        ->pluck('code')
                        ->map(fn($code) =>
                            "<span class='inline-flex items-center rounded-full
                            bg-slate-100 text-slate-800 border border-slate-200
                            dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700
                            px-2.5 py-0.5 text-xs font-medium'>".$code."</span>"
                        )->join(' ');
                })
                ->addColumn('department_list', function ($memo) {
                    static $totalDepartmentCount = null;
                    if ($totalDepartmentCount === null) {
                        $totalDepartmentCount = Department::count();
                    }

                    $departments = $memo->departments->pluck('name');
                    $total = $departments->count();

                    if ($memo->for_all_departments || ($totalDepartmentCount > 0 && $total === $totalDepartmentCount)) {
                        return "<span class='inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 px-2.5 py-0.5 text-xs font-medium'>All</span>";
                    }

                    $pills = $departments->take(2)->map(fn($name) => 
                        "<span class='inline-flex items-center rounded-full bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap'>" . e($name) . "</span>"
                    )->join('');

                    if ($total > 2) {
                        $remaining = $total - 2;
                        $fullList = e($departments->join(', '));
                        $pills .= "<span title='{$fullList}' class='cursor-pointer inline-flex items-center rounded-full bg-slate-200 text-slate-600 border border-slate-300 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800 px-2 py-0.5 text-xs font-medium'>+{$remaining}</span>";
                    }

                    return "<div class='flex flex-wrap gap-1.5 max-w-[220px]'>{$pills}</div>";
                })
                ->addColumn('deleted_at_formatted', fn($memo) => $memo->deleted_at->format('M d, Y'))
                ->addColumn('actions', function($memo){
                    return '
                        <button
                            onclick="restoreMemo('.$memo->id.')"
                            class="inline-flex items-center justify-center
                            w-8 h-8
                            rounded-full
                            bg-emerald-50
                            text-emerald-600
                            border border-emerald-200
                            hover:bg-emerald-100
                            dark:bg-slate-800
                            dark:border-slate-700
                            dark:text-emerald-400">

                            <i class="ri-refresh-line"></i>

                        </button>';
                })
                ->addColumn('memo_no_link', function ($memo) {
                    return '
                    <button
                        class="view-pdf-btn memo-pill inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-slate-800 dark:text-indigo-300 dark:border-slate-700"
                        data-id="'.$memo->id.'"
                        data-memono="'.$memo->memo_no.'"
                        data-title="'.e($memo->title).'">

                        <i class="ri-file-text-line"></i>
                        '.$memo->memo_no.'
                    </button>';
                })
                ->rawColumns(['memo_no_link', 'company_list', 'department_list', 'actions'])
                ->make(true);
        };

        return view('memos.archived');
    }

    public function viewInline($id)
    {
        $memo = Memo::withTrashed()->findOrFail($id);
        abort_unless($memo->isViewableBy(auth()->user()), 403, 'You do not have access to view this memo.');
        return response()->file(storage_path('app/public/'.$memo->file_path));
    }

    public function download($id)
    {
        $memo = Memo::withTrashed()->findOrFail($id);
        abort_unless($memo->isViewableBy(auth()->user()), 403, 'You do not have access to view this memo.');
        return response()->download(storage_path('app/public/'.$memo->file_path), $memo->original_filename);
    }

    public function restore(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $memo = Memo::onlyTrashed()->findOrFail($id);
        $memo->restore();

        MemoLog::create([
            'memo_id' => $memo->id,
            'user_id' => auth()->id(),
            'action' => 'restored',
            'remarks' => $request->remarks,
        ]);

        return response()->json(['success' => true]);
    }

    public function logs(Request $request, $id)
    {
        $query = MemoLog::with('user')->where('memo_id', $id)->latest();

        return DataTables::of($query)
            ->addColumn('user_name', fn($log) => $log->user->name ?? 'Deleted User')
            ->addColumn('action_badge', function($log){
                $colors = [
                    'uploaded' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50',
                    'edited' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/50',
                    'archived' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-900/50',
                    'restored' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50',
                    'viewed' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700',
                ];
                $color = $colors[$log->action] ?? $colors['viewed'];
                return "<span class='inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {$color}'>" . ucfirst($log->action) . "</span>";
            })
            ->addColumn('remarks_display', fn($log) => $log->remarks ? e($log->remarks) : '<span class="text-slate-400 dark:text-slate-600">—</span>')
            ->editColumn('created_at', fn($log) => $log->created_at->format('M d, Y h:i A'))
            ->rawColumns(['action_badge', 'remarks_display'])
            ->make(true);
    }

    private function cleanExtractedText(string $text): string
    {
        $text = preg_replace_callback(
            '/\b(?:[A-Za-z]\s){2,}[A-Za-z]\b/u',
            fn ($m) => str_replace(' ', '', $m[0]),
            $text
        );

        return $text;
    }

    public function show(Memo $memo)
    {
        return response()->file(storage_path('app/public/'.$memo->file_path));
    }
}
