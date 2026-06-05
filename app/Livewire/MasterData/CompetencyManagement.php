<?php

namespace App\Livewire\MasterData;

use App\Exports\CompetenciesExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Competency;
use App\Services\CompetencyService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class CompetencyManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $levelFilter = '';
    public $showModal = false;
    public $editMode = false;

    public $competencyId;
    public $code;
    public $name;
    public $level;
    public $description;
    public $is_active = 1;

    public $showDeleteModal = false;
    public $deletingCompetencyId;
    public $deletingCompetencyName;

    public function mount()
    {
        $this->authorize('viewAny', Competency::class);
    }

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:50', $this->editMode ? 'unique:competencies,code,' . $this->competencyId : 'unique:competencies,code'],
            'name' => 'required|string|max:255',
            'level' => 'nullable|integer|in:1,2,3',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ];
    }

    public function validationAttributes()
    {
        return [
            'code' => 'kode kompetensi',
            'name' => 'nama kompetensi',
            'level' => 'level',
            'description' => 'deskripsi',
            'is_active' => 'status aktif',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLevelFilter()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create', Competency::class);
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $competency = Competency::findOrFail($id);
        $this->authorize('update', $competency);

        $this->competencyId = $competency->id;
        $this->code = $competency->code;
        $this->name = $competency->name;
        $this->level = $competency->level;
        $this->description = $competency->description;
        $this->is_active = $competency->is_active ? 1 : 0;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(CompetencyService $service)
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'name' => $this->name,
                'level' => $this->level,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ];

            if ($this->editMode) {
                $competency = Competency::findOrFail($this->competencyId);
                $this->authorize('update', $competency);
                $service->update($competency, $data);
                $message = 'Kompetensi berhasil diupdate!';
            } else {
                $this->authorize('create', Competency::class);
                $service->create($data);
                $message = 'Kompetensi berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $competency = Competency::findOrFail($id);
        $this->deletingCompetencyId = $competency->id;
        $this->deletingCompetencyName = $competency->name;
        $this->showDeleteModal = true;
    }

    public function delete(CompetencyService $service)
    {
        try {
            $competency = Competency::findOrFail($this->deletingCompetencyId);
            $this->authorize('delete', $competency);

            $service->delete($competency);
            $this->notifySuccess('Kompetensi berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat menghapus kompetensi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id, CompetencyService $service)
    {
        try {
            $competency = Competency::findOrFail($id);
            $this->authorize('toggleStatus', $competency);

            $service->toggleStatus($competency);
            $status = $competency->fresh()->is_active ? 'aktif' : 'non-aktif';
            $this->notifySuccess("Status kompetensi berhasil diubah menjadi {$status}!");
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk mengubah status kompetensi.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'competencyId',
            'code',
            'name',
            'level',
            'description',
            'is_active',
        ]);

        $this->is_active = 1;
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', Competency::class);

        return (new CompetenciesExport($this->search, $this->levelFilter))
            ->download('kompetensi-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(CompetencyService $service)
    {
        $this->authorize('exportPdf', Competency::class);

        $competencies = Competency::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->levelFilter !== null && $this->levelFilter !== '', function ($q) {
                $q->where('level', $this->levelFilter);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('exports.competencies-pdf', ['competencies' => $competencies]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'kompetensi-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(CompetencyService $service)
    {
        $levelOptions = $service->getLevelOptions();

        return view('livewire.master-data.competency-management', [
            'competencies' => $service->getFiltered(
                $this->search,
                $this->levelFilter,
                false
            ),
            'levelOptions' => $levelOptions,
        ])->with('levelOptions', $levelOptions);
    }
}
