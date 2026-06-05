<?php

namespace App\Livewire\MasterData;

use App\Livewire\Traits\HasNotification;
use App\Models\Competency;
use App\Models\Personel;
use App\Services\PersonelService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class PersonelForm extends Component
{
    use WithFileUploads, AuthorizesRequests, HasNotification;

    public $personelId;
    public $editMode = false;

    public $code;
    public $name;
    public $is_active = 1;

    public $competencies = [];
    public $competencyOptions = [];

    public $showDeleteCompetencyModal = false;
    public $competencyToDelete;
    public $deletingCompetencyId;

    public function mount($personel = null)
    {
        if ($personel) {
            $this->editMode = true;
            $this->personelId = $personel->id;
            $this->code = $personel->code;
            $this->name = $personel->name;
            $this->is_active = $personel->is_active ? 1 : 0;

            $this->loadCompetenciesFromDatabase($personel);
        } else {
            $this->authorize('create', Personel::class);
        }

        $this->competencyOptions = Competency::active()->orderBy('name')->get();
    }

    private function loadCompetenciesFromDatabase(Personel $personel): void
    {
        // Preserve new items (temp IDs) that haven't been saved yet
        $newItems = collect($this->competencies)->filter(function ($item) {
            return isset($item['id']) && str_starts_with($item['id'], 'temp_');
        })->toArray();

        // Load existing items from database
        $existingItems = $personel->competencies->map(function ($competency) {
            return [
                'id' => $competency->pivot->id,
                'competency_id' => $competency->id,
                'certificate_file' => null,
                'certificate_file_name' => $competency->pivot->certificate_file_name,
                'certificate_file_path' => $competency->pivot->certificate_file_path,
                'certificate_file_size' => $competency->pivot->certificate_file_size,
                'certificate_file_status' => $competency->pivot->certificate_file_status,
                'certificate_file_error' => $competency->pivot->certificate_file_error,
                'issuer' => $competency->pivot->issuer,
                'expired_date' => $competency->pivot->expired_date,
            ];
        })->toArray();

        // Merge existing items with new items
        $this->competencies = array_merge($existingItems, $newItems);
    }

    public function rules()
    {
        $rules = [
            'code' => ['required', 'string', 'max:50', $this->editMode ? 'unique:personels,code,' . $this->personelId : 'unique:personels,code'],
            'name' => 'required|string|max:255',
            'is_active' => 'required|in:0,1',
            'competencies' => 'array',
            'competencies.*.competency_id' => 'required|exists:competencies,id',
            'competencies.*.issuer' => 'required|string|max:255',
            'competencies.*.expired_date' => 'required|date',
        ];

        // File is required for new competencies (no existing file)
        foreach ($this->competencies as $index => $competency) {
            $hasExistingFile = isset($competency['certificate_file_name']) && !empty($competency['certificate_file_name']);
            if (!$hasExistingFile) {
                $rules["competencies.{$index}.certificate_file"] = file_upload_validation_rule('personel_certificate', true);
            } else {
                $rules["competencies.{$index}.certificate_file"] = file_upload_validation_rule('personel_certificate', false);
            }
        }

        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'code' => 'kode personel',
            'name' => 'nama personel',
            'is_active' => 'status aktif',
            'competencies.*.competency_id' => 'kompetensi',
            'competencies.*.certificate_file' => 'file sertifikat',
            'competencies.*.issuer' => 'penerbit',
            'competencies.*.expired_date' => 'tanggal expired',
        ];
    }

    public function addCompetency()
    {
        $this->competencies[] = [
            'id' => 'temp_' . uniqid(),
            'competency_id' => null,
            'certificate_file' => null,
            'certificate_file_name' => null,
            'certificate_file_path' => null,
            'certificate_file_size' => null,
            'certificate_file_status' => null,
            'certificate_file_error' => null,
            'issuer' => null,
            'expired_date' => null,
        ];
    }

    public function removeCompetencyFile($index)
    {
        if (isset($this->competencies[$index])) {
            $this->competencies[$index]['certificate_file'] = null;
        }
    }

    public function removeCompetency($tempId)
    {
        // If it's an existing record (numeric ID), delete from database
        if (is_numeric($tempId)) {
            $existingRecord = \DB::table('personel_competency')->where('id', $tempId)->first();
            if ($existingRecord) {
                if ($existingRecord->certificate_file_path && \Storage::disk('local')->exists($existingRecord->certificate_file_path)) {
                    \Storage::disk('local')->delete($existingRecord->certificate_file_path);
                }
                \DB::table('personel_competency')->where('id', $tempId)->delete();
            }
        }

        // Remove from array by filtering out the item with matching ID
        $this->competencies = array_values(array_filter($this->competencies, function ($item) use ($tempId) {
            return ($item['id'] ?? '') !== $tempId;
        }));
    }

    public function confirmDeleteCompetency(string $tempId): void
    {
        $this->competencyToDelete = $tempId;
        $this->deletingCompetencyId = $tempId;
        $this->showDeleteCompetencyModal = true;
    }

    public function deleteCompetency(): void
    {
        if ($this->competencyToDelete) {
            $isExistingRecord = is_numeric($this->competencyToDelete);
            $this->removeCompetency($this->competencyToDelete);

            // Only refresh from database if we deleted an existing record
            if ($isExistingRecord && $this->personelId) {
                $personel = Personel::find($this->personelId);
                if ($personel) {
                    $this->loadCompetenciesFromDatabase($personel);
                }
            }

            $this->competencyToDelete = null;
            $this->deletingCompetencyId = null;
            $this->showDeleteCompetencyModal = false;
            $this->notifySuccess('Kompetensi berhasil dihapus.');
        }
    }

    public function cancelDeleteCompetency(): void
    {
        $this->competencyToDelete = null;
        $this->deletingCompetencyId = null;
        $this->showDeleteCompetencyModal = false;
    }

    public function downloadCompetencyFile($index)
    {
        $competency = $this->competencies[$index] ?? null;

        if (!$competency) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        if (!isset($competency['certificate_file_path']) || !$competency['certificate_file_path']) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        if (!\Storage::disk('local')->exists($competency['certificate_file_path'])) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        // Generate new filename: namakompetensi_level_kodepersonel
        $competencyOption = collect($this->competencyOptions)->firstWhere('id', $competency['competency_id']);
        $competencyName = $competencyOption ? $competencyOption->name : 'kompetensi';
        $competencyLevel = $competencyOption ? $competencyOption->level_label : 'level';
        $personelCode = $this->code ?? 'personel';

        // Get original file extension
        $originalExtension = pathinfo($competency['certificate_file_name'], PATHINFO_EXTENSION);
        $newFileName = "{$competencyName}_{$competencyLevel}_{$personelCode}.{$originalExtension}";

        return \Storage::disk('local')->download($competency['certificate_file_path'], $newFileName);
    }

    public function hasProcessingCompetencyFiles(): bool
    {
        return collect($this->competencies)->contains(function ($item) {
            return isset($item['certificate_file_status']) && in_array($item['certificate_file_status'], ['pending', 'processing']);
        });
    }

    public function refreshCompetencyFileStatus(): void
    {
        if ($this->personelId) {
            $personel = Personel::find($this->personelId);
            if ($personel) {
                $this->loadCompetenciesFromDatabase($personel);
            }
        }
    }

    public function save(PersonelService $service)
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'name' => $this->name,
                'is_active' => $this->is_active,
                'competencies' => [],
            ];

            foreach ($this->competencies as $competency) {
                if (empty($competency['competency_id'])) {
                    continue;
                }

                $competencyData = [
                    'competency_id' => $competency['competency_id'],
                    'issuer' => $competency['issuer'],
                    'expired_date' => $competency['expired_date'],
                ];

                if (isset($competency['certificate_file']) && $competency['certificate_file'] instanceof \Illuminate\Http\UploadedFile) {
                    $tempPath = $competency['certificate_file']->store('temp/personel-certificates', 'local');
                    $competencyData['temp_file_path'] = $tempPath;
                    $competencyData['file_name'] = $competency['certificate_file']->getClientOriginalName();
                } elseif (!empty($competency['certificate_file_path'])) {
                    $competencyData['certificate_file_path'] = $competency['certificate_file_path'];
                    $competencyData['certificate_file_name'] = $competency['certificate_file_name'];
                }

                $data['competencies'][] = $competencyData;
            }

            if ($this->editMode) {
                $personel = Personel::findOrFail($this->personelId);
                $this->authorize('update', $personel);
                $service->update($personel, $data);
                $message = 'Personel berhasil diupdate!';
            } else {
                $this->authorize('create', Personel::class);
                $service->create($data);
                $message = 'Personel berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            return $this->redirect(route('master-data.personels.index'), navigate: true);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('master-data.personels.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.master-data.personel-form');
    }
}
