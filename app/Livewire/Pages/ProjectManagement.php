<?php

namespace App\Livewire\Pages;

use App\Enums\ApprovalStatus;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use App\Exports\ProjectsExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Project;
use App\Services\ProjectService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $statusFilter = '';
    public $approvalFilter = '';
    public $riskFilter = '';
    public $priorityFilter = '';
    public bool $filterChanged = false;

    public $showDeleteModal = false;
    public $deletingProjectId;
    public $deletingProjectName;

    public function mount()
    {
        $this->authorize('viewAny', Project::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingApprovalFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingRiskFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->approvalFilter = '';
        $this->riskFilter = '';
        $this->priorityFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(ProjectStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getApprovalStatusOptionsProperty(): array
    {
        return collect(ApprovalStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getRiskLevelOptionsProperty(): array
    {
        return collect(RiskLevel::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function confirmDelete($id)
    {
        $project = Project::findOrFail($id);
        $this->deletingProjectId = $project->id;
        $this->deletingProjectName = $project->name;
        $this->showDeleteModal = true;
    }

    public function delete(ProjectService $service)
    {
        try {
            $project = Project::findOrFail($this->deletingProjectId);
            $this->authorize('delete', $project);

            $service->delete($project);
            $this->notifySuccess('Project berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat menghapus project ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', Project::class);

        return (new ProjectsExport($this->search, $this->statusFilter, $this->riskFilter, $this->approvalFilter, $this->priorityFilter))
            ->download('project-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $this->authorize('exportPdf', Project::class);

        $projects = Project::with(['creator', 'approver'])
            ->withCount('modules')
            ->search($this->search)
            ->byStatus($this->statusFilter)
            ->byApprovalStatus($this->approvalFilter)
            ->byRiskLevel($this->riskFilter)
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('exports.projects-pdf', ['projects' => $projects]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'project-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(ProjectService $service)
    {
        $projects = $service->getFiltered(
            $this->search,
            $this->statusFilter,
            $this->approvalFilter,
            $this->riskFilter,
            $this->priorityFilter
        );

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$projects->total()} data project.");
            $this->filterChanged = false;
        }

        return view('livewire.pages.project-management', [
            'projects' => $projects,
        ]);
    }
}
