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
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingApprovalFilter()
    {
        $this->resetPage();
    }

    public function updatingRiskFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
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

        return (new ProjectsExport($this->search, $this->statusFilter, $this->riskFilter))
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
        return view('livewire.pages.project-management', [
            'projects' => $service->getFiltered(
                $this->search,
                $this->statusFilter,
                $this->approvalFilter,
                $this->riskFilter,
                $this->priorityFilter
            ),
            'riskLevels' => RiskLevel::cases(),
            'statuses' => ProjectStatus::options(),
            'approvalStatuses' => collect(ApprovalStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->toArray(),
        ]);
    }
}
