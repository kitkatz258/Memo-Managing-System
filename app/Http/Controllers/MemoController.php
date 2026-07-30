<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\EmployeeRank;
use App\Models\Memo;
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
            $query = Memo::with(['companies', 'categories', 'employeeRanks', 'supersededMemos']);

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
                        $query->whereRaw(
                            "MATCH(title, extracted_content) AGAINST(? IN NATURAL LANGUAGE MODE)",
                            [$search]
                        );
                    }
                })
                ->addColumn('title_link', fn($memo) => '<a href="'.route('memos.show', $memo->id).'" class="text-primary underline" target="_blank">'.e($memo->title).'</a>')
                ->addColumn('company_list', fn($memo) => $memo->for_all_companies ? 'All' : $memo->companies->pluck('code')->join(', '))
                ->addColumn('category_list', fn($memo) => $memo->for_all_categories ? 'All' : $memo->categories->pluck('name')->join(', '))
                ->addColumn('rank_list', fn($memo) => $memo->for_all_ranks ? 'All' : $memo->employeeRanks->pluck('name')->join(', '))
                ->addColumn('superseded_list', fn($memo) => $memo->supersededMemos->pluck('title')->join(', ') ?: '—')
                ->addColumn('related_list', fn($memo) => $memo->relatedMemos()->pluck('title')->join(', ') ?: '—')
                ->addColumn('created_at_formatted', fn($memo) => $memo->created_at->format('M d, Y'))
                ->addColumn('actions', function ($memo) {
                    return '
                        <button onclick="Livewire.dispatch(\'open-memo-modal-edit\', { memoId: '.$memo->id.' })" class="text-blue-600 hover:underline mr-3">Edit</button>
                        <button onclick="archiveMemo('.$memo->id.')" class="text-red-600 hover:underline">Archive</button>
                    ';
                })
                ->rawColumns(['title_link', 'actions'])
                ->make(true);
        }

        $companies = Company::all();
        $categories = Category::all();
        $employeeRanks = EmployeeRank::all();
        return view('memos.index', compact('companies', 'categories', 'employeeRanks'));
    }

    public function archive(Memo $memo)
    {
        $memo->delete();
        return response()->json(['success' => true]);
    }

    public function show(Memo $memo)
    {
        return response()->file(storage_path('app/public/'.$memo->file_path));
    }
}
