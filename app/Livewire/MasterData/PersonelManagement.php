<?php

namespace App\Livewire\MasterData;

use App\Exports\PersonelsExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Personel;
use App\Services\PersonelService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class PersonelManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $competencyFilter = '';
    public $isActiveFilter = '';
    public bool $filterChanged = false;
    public $competencyOptions = [];
    public $showDeleteModal = false;
    public $deletingPersonelId;
    public $deletingPersonelName;

    public function mount()
    {
        $this->authorize('viewAny', Personel::class);
        $this->competencyOptions = \App\Models\Competency::active()->orderBy('name')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatedCompetencyFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatedIsActiveFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function resetFilters()
    {
        $this->competencyFilter = '';
        $this->isActiveFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getIsActiveOptionsProperty(): array
    {
        return [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Nonaktif'],
        ];
    }

    public function create()
    {
        $this->authorize('create', Personel::class);
        return $this->redirect(route('master-data.personels.create'), navigate: true);
    }

    public function edit($id)
    {
        $personel = Personel::findOrFail($id);
        $this->authorize('update', $personel);

        return $this->redirect(route('master-data.personels.edit', $personel), navigate: true);
    }

    public function confirmDelete($id)
    {
        $personel = Personel::findOrFail($id);
        $this->deletingPersonelId = $personel->id;
        $this->deletingPersonelName = $personel->name;
        $this->showDeleteModal = true;
    }

    public function delete(PersonelService $service)
    {
        try {
            $personel = Personel::findOrFail($this->deletingPersonelId);
            $this->authorize('delete', $personel);

            $service->delete($personel);
            $this->notifySuccess('Personel berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat menghapus personel ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function toggleStatus($id, PersonelService $service)
    {
        try {
            $personel = Personel::findOrFail($id);
            $this->authorize('toggleStatus', $personel);

            $service->toggleStatus($personel);
            $status = $personel->fresh()->is_active ? 'aktif' : 'non-aktif';
            $this->notifySuccess("Status personel berhasil diubah menjadi {$status}!");
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk mengubah status personel.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', Personel::class);

        return (new PersonelsExport($this->search, $this->competencyFilter ? (int)$this->competencyFilter : null, $this->isActiveFilter !== '' ? $this->isActiveFilter : null))
            ->download('personel-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $this->authorize('exportPdf', Personel::class);

        $personels = Personel::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->competencyFilter, function ($q) {
                $q->whereHas('competencies', function ($subQ) {
                    $subQ->where('competencies.id', (int)$this->competencyFilter);
                });
            })
            ->when($this->isActiveFilter !== null && $this->isActiveFilter !== '', function ($q) {
                $q->where('is_active', $this->isActiveFilter === '1');
            })
            ->with('competencies')
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('exports.personels-pdf', ['personels' => $personels]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'personel-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(PersonelService $service)
    {
        $personels = $service->getFiltered($this->search, $this->isActiveFilter !== '' ? $this->isActiveFilter : null, $this->competencyFilter ? (int)$this->competencyFilter : null);

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$personels->total()} data personel.");
            $this->filterChanged = false;
        }

        return view('livewire.master-data.personel-management', [
            'personels' => $personels,
        ]);
    }
}
