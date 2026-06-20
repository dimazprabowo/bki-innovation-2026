<?php

namespace App\Livewire\Pages;

use App\Livewire\Traits\HasNotification;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ProjectDetail extends Component
{
    use AuthorizesRequests, HasNotification;

    public Project $project;

    public $showRejectModal = false;
    public $rejectionReason = '';

    public $showCloseModal = false;
    public $closeReason = '';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project->load([
            'creator',
            'approver',
            'modules.personels.competencies',
            'modules.tools.peralatan',
            'modules.deliverables',
            'modules.workOrderItems.subitems',
            'modules.workOrderReferences',
            'projectPersonels.personel',
            'projectPersonels.module',
            'projectPersonels.personelSlot',
            'projectPeralatans.peralatan',
            'projectPeralatans.module',
            'projectPeralatans.tool',
            'additionalCosts',
        ]);
    }

    public function goBack()
    {
        return $this->redirect(route('projects.index'), navigate: true);
    }

    public function submit(ProjectService $service)
    {
        try {
            $this->authorize('update', $this->project);
            $service->submit($this->project);

            $freshProject = $this->project->fresh();
            $message = $freshProject->requiresCoEControl()
                ? 'Project berhasil diajukan dan masuk ke CoE Review!'
                : 'Project berhasil diajukan dan otomatis disetujui!';

            $this->notifySuccess($message);
            $this->project = $freshProject->load([
                'creator',
                'approver',
                'modules.personels.competencies',
                'modules.tools.peralatan',
                'modules.deliverables',
                'modules.workOrderItems.subitems',
                'modules.workOrderReferences',
                'projectPersonels.personel',
                'projectPersonels.module',
                'projectPersonels.personelSlot',
                'projectPeralatans.peralatan',
                'projectPeralatans.module',
                'projectPeralatans.tool',
                'additionalCosts',
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat mengajukan project ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve(ProjectService $service)
    {
        try {
            $this->authorize('approve', $this->project);
            $service->approve($this->project, auth()->id());
            $this->notifySuccess('Project berhasil disetujui!');
            $this->project = $this->project->fresh()->load([
                'creator',
                'approver',
                'modules.personels.competencies',
                'modules.tools.peralatan',
                'modules.deliverables',
                'modules.workOrderItems.subitems',
                'modules.workOrderReferences',
                'projectPersonels.personel',
                'projectPersonels.module',
                'projectPersonels.personelSlot',
                'projectPeralatans.peralatan',
                'projectPeralatans.module',
                'projectPeralatans.tool',
                'additionalCosts',
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk menyetujui project.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmReject()
    {
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function reject(ProjectService $service)
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500',
        ], [
            'rejectionReason.required' => 'Alasan penolakan harus diisi.',
            'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejectionReason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        try {
            $this->authorize('reject', $this->project);
            $service->reject($this->project, $this->rejectionReason);
            $this->notifySuccess('Project berhasil ditolak!');
            $this->showRejectModal = false;
            $this->project = $this->project->fresh()->load([
                'creator',
                'approver',
                'modules',
                'projectPersonels.personel',
                'projectPersonels.module',
                'projectPersonels.personelSlot',
                'projectPeralatans.peralatan',
                'projectPeralatans.module',
                'projectPeralatans.tool',
                'additionalCosts',
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk menolak project.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmClose()
    {
        $this->closeReason = '';
        $this->showCloseModal = true;
    }

    public function closeProject(ProjectService $service)
    {
        $this->validate([
            'closeReason' => 'required|string|min:10|max:500',
        ], [
            'closeReason.required' => 'Alasan penutupan harus diisi.',
            'closeReason.min' => 'Alasan penutupan minimal 10 karakter.',
            'closeReason.max' => 'Alasan penutupan maksimal 500 karakter.',
        ]);

        try {
            $this->authorize('close', $this->project);
            $service->close($this->project, $this->closeReason);
            $this->notifySuccess('Project berhasil ditutup!');
            $this->showCloseModal = false;
            $this->project = $this->project->fresh()->load([
                'creator',
                'approver',
                'modules',
                'projectPersonels.personel',
                'projectPersonels.module',
                'projectPersonels.personelSlot',
                'projectPeralatans.peralatan',
                'projectPeralatans.module',
                'projectPeralatans.tool',
                'additionalCosts',
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk menutup project.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.project-detail');
    }
}
