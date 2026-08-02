<?php

namespace App\Livewire\Modals;

use App\Models\Memo;
use App\Models\Company;
use App\Models\Category;
use App\Models\EmployeeRank;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Smalot\PdfParser\Parser;

class MemoFormModal extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public ?int $editingMemoId = null;

    public string $title = '';
    public string $memoNo = '';
    public ?int $year = null;
    public string $author = '';
    public $file;
    public ?string $existingFileName = null;

    public array $selectedCompanies = [];
    public bool $forAllCompanies = false;
    public array $selectedCategories = [];
    public bool $forAllCategories = false;
    public array $selectedRanks =[];
    public bool $forAllRanks = false;
    public array $selectedSupersededMemos = [];
    public array $selectedRelatedMemos = [];

    public $companies = [];
    public $categories = [];
    public $employeeRanks = [];
    public $existingMemos = [];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'memoNo' => 'nullable|string|max:100',
            'year' => 'required|integer|min:1986|max:' . (now()->year + 1),
            'author' => 'nullable|string|max:255',
            'file' => ($this->editingMemoId ? 'nullable' : 'required') . '|file|mimes:pdf|max:40960',
            'selectedCompanies' => $this->forAllCompanies ? 'nullable|array' : 'required|array|min:1',
            'selectedCategories' => $this->forAllCompanies ? 'nullable|array' : 'required|array|min:1',
            'selectedRanks' => $this->forAllRanks ? 'nullable|array' : 'required|array|min:1',
        ];
    }

    public function mount()
    {
        $this->companies = Company::all();
        $this->categories = Category::all();
        $this->employeeRanks = EmployeeRank::all();
        $this->year = now()->year;
    }

    #[On('open-memo-modal')]
    public function openCreate()
    {
        $this->resetForm();
        $this->existingMemos = Memo::select('id', 'title')->get();
        $this->showModal = true;
    }

    #[On('open-memo-modal-edit')]
    public function openEdit($memoId)
    {
        $this->resetForm();

        $memo = Memo::with(['companies', 'categories', 'employeeRanks', 'supersededMemos', 'relatedMemos'])->findOrFail($memoId);

        $this->editingMemoId = $memo->id;
        $this->title = $memo->title;
        $this->memoNo = $memo->memo_no ?? '';
        $this->year = $memo->year;
        $this->author = $memo->author ?? '';
        $this->existingFileName = $memo->original_filename;

        $this->forAllCompanies = $memo->for_all_companies;
        $this->selectedCompanies = $memo->companies->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->dispatch('set-company-values', ids: $this->selectedCompanies);

        $this->forAllCategories = $memo->for_all_categories
            ? Category::pluck('id')->map(fn($id) => (string) $id)->toArray()
            : $memo->categorues->pluck('id')->map(fn($id) => (string) $id)->toArray;
        $this->selectedCategories = $memo->categories->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->dispatch('set-category-values', ids: $this->selectedCategories);

        $this->forAllRanks = $memo->for_all_ranks;
        $this->selectedRanks = $memo->employeeRanks->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->dispatch('set-rank-values', ids: $this->selectedRanks);

        $this->selectedSupersededMemos = $memo->supersededMemos->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->dispatch('set-superseded-values', items: $memo->supersededMemos->map(fn($m) => ['id' => $m->id, 'title' => $m->title])->toArray());

        $this->selectedRelatedMemos = DB::table('memo_relations')
            ->where('memo_id', $memo->id)
            ->pluck('related_memo_id')
            ->toArray();
        $this->dispatch('set-related-values', items: $memo->map(fn($m) => ['id' => $m->id, 'title' => $m->title])->toArray());

        $this->showModal = true;
    }

    public function updatedForAllCompanies($value)
    {
        if($value) 
            $this->selectedCompanies = [];
    }

    public function updatedForAllCategories($value)
    {
        if($value)
            $this->selectedCategories = [];
    }

    public function updatedForAllRanks($value)
    {
        if($value)
            $this->selectedRanks = [];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'memo_no' => $this->memoNo,
            'year' => $this->year,
            'author' => $this->author,
            'for_all_companies' => $this->forAllCompanies,
            'for_all_categories' => $this->forAllCategories,
            'for_all_ranks' => $this->forAllRanks,
        ];

        if($this->file){
            $originalName = $this->file->getClientOriginalName();
            $safeName = Str::uuid() . '.' . $this->file->getClientOriginalExtension();
            $path = $this->file->storeAs('memos', $safeName, 'public');

            $extractedContent = null;
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($this->file->getRealPath());
                $extractedContent = $pdf->getText();
            } catch (\Exception $e) {

            }

            $data['file_path'] = $path;
            $data['original_filename'] = $originalName;
            $data['extracted_content'] = $extractedContent;
        }

        if($this->editingMemoId) {
            $memo = Memo::findOrFail($this->editingMemoId);
            $memo->update($data);
        } else {
            $data['uploaded_by'] = auth()->id();
            $memo = Memo::create($data);
        }

        $memo->companies()->sync($this->forAllCompanies ? [] : $this->selectedCompanies);
        $memo->categories()->sync($this->selectedCategories);
        $memo->employeeRanks()->sync($this->forAllRanks ? [] : $this->selectedRanks);
        $memo->supersededMemos()->sync($this->selectedSupersededMemos);

        DB::table('memo_relations')->where('memo_id', $memo->id)->orWhere('related_memo_id', $memo->id)->delete();
        foreach($this->selectedRelatedMemos as $relatedId) {
            DB::table('memo_relations')->insert([
                ['memo_id' => $memo->id, 'related_memo_id' => $relatedId, 'created_at' => now(), 'updated_at' => now()],
                ['memo_id' => $relatedId, 'related_memo_id' => $memo->id, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        $this->showModal = false;
        $this->dispatch('memo-saved');
        session()->flash('success', 'Memo saved successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->reset([
            'editingMemoId', 'title', 'memoNo', 'author', 'file', 'existingFileName',
            'selectedCompanies', 'forAllCompanies', 'selectedCategories', 'forAllCategories',
            'selectedRanks', 'forAllRanks', 'selectedSupersededMemos', 'selectedRelatedMemos',
        ]);
        $this->year = now()->year;
        $this->resetValidation();
    }

    public function searchPicker(Request $request)
    {
        $query = Memo::query();

        if($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if($exclude = $request->get('exclude')) {
            $query->where('id', '!=', $exclude);
        }

        return $query->select('id', 'title')->limit(20)->get();
    }

    public function render()
    {
        return view('livewire.modals.memo-form-modal');
    }
}
