<?php

namespace App\Livewire\Pages;

use App\Livewire\Traits\HasNotification;
use App\Models\Project;
use App\Models\ProjectDeliverable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProjectDeliverables extends Component
{
    use AuthorizesRequests, HasNotification, WithFileUploads;

    public Project $project;

    public array $uploads = [];
    public array $notes = [];

    public $actualEndDate;
    public $projectNotes;

    public $showDeleteModal = false;
    public $deletingDeliverableId = null;

    public function mount(Project $project): void
    {
        $this->authorize('manageDeliverables', $project);
        $this->project = $project->load([
            'modules.deliverables',
            'projectDeliverables.uploader',
        ]);
        $this->actualEndDate = $this->project->actual_end_date?->format('Y-m-d');
        $this->projectNotes = $this->project->notes;
    }

    protected function rules(): array
    {
        $rules = [];

        foreach ($this->project->modules as $module) {
            foreach ($module->deliverables->filter(fn ($d) => $d->is_active) as $deliverable) {
                $key = $deliverable->id;
                $rules["uploads.{$key}"] = 'nullable|file|max:' . get_max_upload_size('project_deliverable')
                    . '|mimes:' . get_allowed_mimes('project_deliverable');
                $rules["notes.{$key}"] = 'nullable|string|max:500';
            }
        }

        return $rules;
    }

    protected function getDeliverableName(int $deliverableId): string
    {
        foreach ($this->project->modules as $module) {
            $deliverable = $module->deliverables->firstWhere('id', $deliverableId);
            if ($deliverable) {
                return $deliverable->name;
            }
        }
        return 'deliverable';
    }

    public function updatedNotes($value, string $key): void
    {
        $deliverableId = $key;
        $deliverableName = $this->getDeliverableName((int) $deliverableId);

        try {
            $this->validate([
                "notes.{$deliverableId}" => 'nullable|string|max:500',
            ], [
                "notes.{$deliverableId}.max" => "Catatan untuk deliverable \"{$deliverableName}\" maksimal 500 karakter.",
            ], [
                "notes.{$deliverableId}" => "catatan deliverable \"{$deliverableName}\"",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    public function uploadDeliverable(int $moduleDeliverableId, int $moduleId): void
    {
        $deliverableName = $this->getDeliverableName($moduleDeliverableId);

        try {
            $this->validate([
                "uploads.{$moduleDeliverableId}" => 'required|file|max:' . get_max_upload_size('project_deliverable')
                    . '|mimes:' . get_allowed_mimes('project_deliverable'),
                "notes.{$moduleDeliverableId}" => 'nullable|string|max:500',
            ], [
                "uploads.{$moduleDeliverableId}.required" => "File untuk deliverable \"{$deliverableName}\" wajib diunggah.",
                "uploads.{$moduleDeliverableId}.file" => "File untuk deliverable \"{$deliverableName}\" tidak valid.",
                "uploads.{$moduleDeliverableId}.max" => "Ukuran file untuk deliverable \"{$deliverableName}\" melebihi batas maksimal.",
                "uploads.{$moduleDeliverableId}.mimes" => "Format file untuk deliverable \"{$deliverableName}\" tidak diizinkan.",
                "notes.{$moduleDeliverableId}.max" => "Catatan untuk deliverable \"{$deliverableName}\" maksimal 500 karakter.",
            ], [
                "uploads.{$moduleDeliverableId}" => "file deliverable \"{$deliverableName}\"",
                "notes.{$moduleDeliverableId}" => "catatan deliverable \"{$deliverableName}\"",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->notifyValidationError($e);
            return;
        }

        try {
            $file = $this->uploads[$moduleDeliverableId];

            $fileName = $file->getClientOriginalName();
            $tempPath = $file->store('temp/project-deliverables', 'local');

            $deliverable = ProjectDeliverable::create([
                'project_id' => $this->project->id,
                'module_id' => $moduleId,
                'module_deliverable_id' => $moduleDeliverableId,
                'file_path' => '',
                'file_name' => $fileName,
                'file_size' => null,
                'file_status' => 'processing',
                'uploaded_by' => auth()->id(),
                'notes' => $this->notes[$moduleDeliverableId] ?? null,
            ]);

            \App\Jobs\ProcessProjectDeliverable::dispatch($deliverable->id, $tempPath, $fileName);

            unset($this->uploads[$moduleDeliverableId]);
            unset($this->notes[$moduleDeliverableId]);

            $this->project = $this->project->fresh(['modules.deliverables', 'projectDeliverables.uploader']);

            $this->notifySuccess('File sedang diproses. Anda akan melihat hasilnya sebentar lagi.');
        } catch (\Exception $e) {
            $this->notifyError('Gagal mengupload file. Silakan coba lagi.');
        }
    }

    public function confirmDelete(int $deliverableId): void
    {
        $this->deletingDeliverableId = $deliverableId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingDeliverableId = null;
    }

    public function deleteDeliverable(): void
    {
        $deliverable = ProjectDeliverable::findOrFail($this->deletingDeliverableId);

        if ($deliverable->project_id !== $this->project->id) {
            $this->notifyError('Deliverable tidak valid.');
            $this->showDeleteModal = false;
            return;
        }

        if ($deliverable->file_path && Storage::disk(file_disk())->exists($deliverable->file_path)) {
            Storage::disk(file_disk())->delete($deliverable->file_path);
        }

        $deliverable->delete();

        $this->project = $this->project->fresh(['modules.deliverables', 'projectDeliverables.uploader']);

        $this->notifySuccess('Deliverable berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deletingDeliverableId = null;
    }

    public function refreshFileStatus(): void
    {
        $hasProcessing = $this->project->projectDeliverables()
            ->where('file_status', 'processing')
            ->exists();

        if ($hasProcessing) {
            $this->project = $this->project->fresh(['modules.deliverables', 'projectDeliverables.uploader']);
        }
    }

    public function downloadDeliverable(int $deliverableId)
    {
        $deliverable = ProjectDeliverable::findOrFail($deliverableId);

        if ($deliverable->project_id !== $this->project->id) {
            $this->notifyError('Deliverable tidak valid.');
            return;
        }

        if (!Storage::disk(file_disk())->exists($deliverable->file_path)) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        return Storage::disk(file_disk())->download($deliverable->file_path, $deliverable->file_name);
    }

    public function previewDeliverable(int $deliverableId): void
    {
        $deliverable = ProjectDeliverable::findOrFail($deliverableId);

        if ($deliverable->project_id !== $this->project->id) {
            $this->notifyError('Deliverable tidak valid.');
            return;
        }

        if (!Storage::disk(file_disk())->exists($deliverable->file_path)) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        $extension = strtolower(pathinfo($deliverable->file_name, PATHINFO_EXTENSION));
        $previewable = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'pdf'];

        if (!in_array($extension, $previewable)) {
            $this->notifyInfo('File ini tidak dapat dipreview. Silakan download untuk melihat.');
            return;
        }

        $mimeType = Storage::disk(file_disk())->mimeType($deliverable->file_path);
        $fileContent = Storage::disk(file_disk())->get($deliverable->file_path);
        $base64 = base64_encode($fileContent);
        $dataUri = 'data:' . $mimeType . ';base64,' . $base64;

        $this->dispatch('open-preview', fileName: $deliverable->file_name, dataUri: $dataUri, isPdf: $extension === 'pdf');
    }

    #[\Livewire\Attributes\Computed]
    public function moduleGroups(): array
    {
        $groups = [];

        foreach ($this->project->modules as $module) {
            $deliverables = $module->deliverables->filter(fn ($d) => $d->is_active);

            if ($deliverables->isEmpty()) {
                continue;
            }

            $uploadedFiles = $this->project->projectDeliverables
                ->where('module_id', $module->id)
                ->groupBy('module_deliverable_id');

            $groups[] = [
                'module' => $module,
                'deliverables' => $deliverables,
                'uploadedFiles' => $uploadedFiles,
            ];
        }

        return $groups;
    }

    public function saveProjectCompletion(): void
    {
        $this->authorize('manageDeliverables', $this->project);

        try {
            $this->validate([
                'actualEndDate' => 'required|date|after_or_equal:' . $this->project->start_date?->format('Y-m-d'),
                'projectNotes' => 'nullable|string|max:1000',
            ], [
                'actualEndDate.required' => 'Tanggal selesai aktual wajib diisi.',
                'actualEndDate.date' => 'Format tanggal tidak valid.',
                'actualEndDate.after_or_equal' => 'Tanggal selesai aktual tidak boleh sebelum tanggal mulai project.',
                'projectNotes.max' => 'Catatan project maksimal 1000 karakter.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->notifyValidationError($e);
            return;
        }

        $this->project->update([
            'actual_end_date' => $this->actualEndDate,
            'notes' => $this->projectNotes ?: null,
        ]);

        $this->project = $this->project->fresh(['modules.deliverables', 'projectDeliverables.uploader']);
        $this->notifySuccess('Data completion project berhasil disimpan.');
    }

    public function goBack()
    {
        return $this->redirect(route('projects.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.project-deliverables');
    }
}
