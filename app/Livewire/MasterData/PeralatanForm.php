<?php

namespace App\Livewire\MasterData;

use App\Livewire\Traits\HasNotification;
use App\Models\Peralatan;
use App\Services\PeralatanService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class PeralatanForm extends Component
{
    use WithFileUploads, AuthorizesRequests, HasNotification;

    public $peralatanId;
    public $editMode = false;

    public $code;
    public $name;
    public $description;
    public $location;
    public $calibration_status = 'not_required';
    public $calibration_expired_date;
    public $condition = 'suitable';
    public $ownership_status = 'owned';
    public $is_active = 1;

    public $evidences = [];

    public $showDeleteEvidenceModal = false;
    public $evidenceToDelete;
    public $deletingEvidenceId;

    public function mount($peralatan = null)
    {
        if ($peralatan) {
            $this->editMode = true;
            $this->peralatanId = $peralatan->id;
            $this->code = $peralatan->code;
            $this->name = $peralatan->name;
            $this->description = $peralatan->description;
            $this->location = $peralatan->location;
            $this->calibration_status = $peralatan->calibration_status->value;
            $this->calibration_expired_date = $peralatan->calibration_expired_date?->format('Y-m-d');
            $this->condition = $peralatan->condition->value;
            $this->ownership_status = $peralatan->ownership_status->value;
            $this->is_active = $peralatan->is_active ? 1 : 0;

            $this->loadEvidencesFromDatabase($peralatan);
        } else {
            $this->authorize('create', Peralatan::class);
        }
    }

    private function loadEvidencesFromDatabase(Peralatan $peralatan): void
    {
        // Preserve new items (temp IDs) that haven't been saved yet
        $newItems = collect($this->evidences)->filter(function ($item) {
            return isset($item['id']) && str_starts_with($item['id'], 'temp_');
        })->toArray();

        // Load existing items from database
        $existingItems = $peralatan->evidences->map(function ($evidence) {
            return [
                'id' => $evidence->id,
                'name' => $evidence->name,
                'file' => null,
                'file_name' => $evidence->file_name,
                'file_path' => $evidence->file_path,
                'file_size' => $evidence->file_size,
                'file_status' => $evidence->file_status,
                'file_error' => $evidence->file_error,
            ];
        })->toArray();

        // Merge existing items with new items
        $this->evidences = array_merge($existingItems, $newItems);
    }

    public function rules()
    {
        $rules = [
            'code' => ['required', 'string', 'max:50', $this->editMode ? 'unique:peralatans,code,' . $this->peralatanId : 'unique:peralatans,code'],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'calibration_status' => 'required|in:calibrated,expired,pending,not_required',
            'calibration_expired_date' => 'nullable|date|required_if:calibration_status,calibrated,pending',
            'condition' => 'required|in:suitable,not_suitable',
            'ownership_status' => 'required|in:owned,rented,borrowed,leased',
            'is_active' => 'required|in:0,1',
            'evidences' => 'array',
            'evidences.*.name' => 'required|string|max:255',
        ];

        // File is required for new evidences (no existing file)
        foreach ($this->evidences as $index => $evidence) {
            $hasExistingFile = isset($evidence['file_name']) && !empty($evidence['file_name']);
            if (!$hasExistingFile) {
                $rules["evidences.{$index}.file"] = file_upload_validation_rule('peralatan_evidence', true);
            } else {
                $rules["evidences.{$index}.file"] = file_upload_validation_rule('peralatan_evidence', false);
            }
        }

        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'code' => 'kode alat',
            'name' => 'nama alat',
            'description' => 'deskripsi',
            'location' => 'lokasi',
            'calibration_status' => 'status kalibrasi',
            'calibration_expired_date' => 'tanggal expired kalibrasi',
            'condition' => 'kondisi alat',
            'ownership_status' => 'status kepemilikan',
            'is_active' => 'status aktif',
            'evidences.*.name' => 'nama evidence',
            'evidences.*.file' => 'file evidence',
        ];
    }

    public function addEvidence()
    {
        $this->evidences[] = [
            'id' => 'temp_' . uniqid(),
            'name' => null,
            'file' => null,
            'file_name' => null,
            'file_path' => null,
            'file_size' => null,
        ];
    }

    public function removeEvidenceFile($index)
    {
        if (isset($this->evidences[$index])) {
            $this->evidences[$index]['file'] = null;
        }
    }

    public function removeEvidence($tempId)
    {
        // If it's an existing record (numeric ID), delete from database
        if (is_numeric($tempId)) {
            $existingRecord = \App\Models\PeralatanEvidence::find($tempId);
            if ($existingRecord) {
                if (\Storage::disk('local')->exists($existingRecord->file_path)) {
                    \Storage::disk('local')->delete($existingRecord->file_path);
                }
                $existingRecord->delete();
            }
        }

        // Remove from array by filtering out the item with matching ID
        $this->evidences = array_values(array_filter($this->evidences, function ($item) use ($tempId) {
            return ($item['id'] ?? '') !== $tempId;
        }));
    }

    public function confirmDeleteEvidence(string $tempId): void
    {
        $this->evidenceToDelete = $tempId;
        $this->deletingEvidenceId = $tempId;
        $this->showDeleteEvidenceModal = true;
    }

    public function deleteEvidence(): void
    {
        if ($this->evidenceToDelete) {
            $isExistingRecord = is_numeric($this->evidenceToDelete);
            $this->removeEvidence($this->evidenceToDelete);

            // Only refresh from database if we deleted an existing record
            if ($isExistingRecord && $this->peralatanId) {
                $peralatan = Peralatan::find($this->peralatanId);
                if ($peralatan) {
                    $this->loadEvidencesFromDatabase($peralatan);
                }
            }

            $this->evidenceToDelete = null;
            $this->deletingEvidenceId = null;
            $this->showDeleteEvidenceModal = false;
            $this->notifySuccess('Evidence berhasil dihapus.');
        }
    }

    public function cancelDeleteEvidence(): void
    {
        $this->evidenceToDelete = null;
        $this->deletingEvidenceId = null;
        $this->showDeleteEvidenceModal = false;
    }

    public function downloadEvidenceFile($index)
    {
        $evidence = $this->evidences[$index] ?? null;

        if (!$evidence) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        if (!isset($evidence['file_path']) || !$evidence['file_path']) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        if (!\Storage::disk('local')->exists($evidence['file_path'])) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        // Generate new filename: namaalat_namaevidence
        $peralatanName = $this->name ?? 'peralatan';
        $evidenceName = $evidence['name'] ?? 'evidence';

        // Get original file extension
        $originalExtension = pathinfo($evidence['file_name'], PATHINFO_EXTENSION);
        $newFileName = "{$peralatanName}_{$evidenceName}.{$originalExtension}";

        return \Storage::disk('local')->download($evidence['file_path'], $newFileName);
    }

    public function hasProcessingEvidenceFiles(): bool
    {
        return collect($this->evidences)->contains(function ($item) {
            return isset($item['file_status']) && in_array($item['file_status'], ['pending', 'processing']);
        });
    }

    public function refreshEvidenceFileStatus(): void
    {
        $this->refreshEvidences();
    }

    protected function refreshEvidences(): void
    {
        // Preserve new items that haven't been saved yet (ID starts with 'temp_')
        $newItems = collect($this->evidences)->filter(function ($item) {
            return isset($item['id']) && !is_numeric($item['id']);
        })->values()->toArray();

        // Reload existing items from database
        if ($this->peralatanId) {
            $peralatan = Peralatan::find($this->peralatanId);
            if ($peralatan) {
                $peralatan->load('evidences');

                $existingItems = $peralatan->evidences->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'file' => null,
                        'file_name' => $item->file_name,
                        'file_path' => $item->file_path,
                        'file_size' => $item->file_size,
                        'file_status' => $item->file_status,
                        'file_error' => $item->file_error,
                    ];
                })->toArray();

                // Merge existing items with new items
                $this->evidences = array_merge($existingItems, $newItems);
            }
        }
    }

    public function getCalibrationStatusOptionsProperty(): array
    {
        return collect(\App\Enums\CalibrationStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getConditionOptionsProperty(): array
    {
        return collect(\App\Enums\EquipmentCondition::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getOwnershipStatusOptionsProperty(): array
    {
        return collect(\App\Enums\OwnershipStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getIsActiveOptionsProperty(): array
    {
        return [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Tidak Aktif'],
        ];
    }

    public function save(PeralatanService $service)
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'location' => $this->location,
                'calibration_status' => $this->calibration_status,
                'calibration_expired_date' => $this->calibration_expired_date,
                'condition' => $this->condition,
                'ownership_status' => $this->ownership_status,
                'is_active' => $this->is_active,
                'evidences' => [],
            ];

            foreach ($this->evidences as $evidence) {
                if (empty($evidence['name'])) {
                    continue;
                }

                $evidenceData = [
                    'name' => $evidence['name'],
                ];

                if (isset($evidence['file']) && $evidence['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $tempPath = $evidence['file']->store('temp/peralatan-evidence', 'local');
                    $evidenceData['temp_file_path'] = $tempPath;
                    $evidenceData['file_name'] = $evidence['file']->getClientOriginalName();
                    $evidenceData['id'] = $evidence['id'] ?? null;
                } elseif (!empty($evidence['file_path'])) {
                    $evidenceData['file_path'] = $evidence['file_path'];
                    $evidenceData['file_name'] = $evidence['file_name'];
                    $evidenceData['file_size'] = $evidence['file_size'];
                    $evidenceData['id'] = $evidence['id'] ?? null;
                }

                $data['evidences'][] = $evidenceData;
            }

            if ($this->editMode) {
                $peralatan = Peralatan::findOrFail($this->peralatanId);
                $this->authorize('update', $peralatan);
                $service->update($peralatan, $data);
                $message = 'Peralatan berhasil diupdate!';
            } else {
                $this->authorize('create', Peralatan::class);
                $service->create($data);
                $message = 'Peralatan berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            return $this->redirect(route('master-data.peralatan.index'), navigate: true);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('master-data.peralatan.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.master-data.peralatan-form');
    }
}
