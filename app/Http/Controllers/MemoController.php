<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
        if ($request->ajax()){
            $query = Memo::query();

            if ($companyId = $request->get('company_id')){
                $query->where(function ($q) use ($companyId){
                    $q->where('for_all_companies', true)
                      ->orWhereHas('companues', fn($q2) => $q2->where('companies.id', $companyId));
                });
            }

            return DataTables::of($query)
                ->filter(function ($query) use ($request){
                    if ($search = $request->get('search')['value'] ?? null){
                        $query->whereRaw(
                            "MATCH(title, extracted_content) AGAINST(? IN NATURAL LANGUAGE MODE)",
                            [$search]
                        );
                    }
                })
                ->addColumn('title_link', function ($memo){
                    return '<a href="'.route('memos.show', $memo->id).'" class="text-blue-600 underline">View</a>';   
                })
                ->rawColumns(['title_link'])
                ->make(true);
        }

        $companies = Company::all();
        return view('memos.index', compact('companies'));
    }

    public function show(Memo $memo)
    {
        return response()->file(storage_path('app/public/'.$memo->file_path));
    }
}
