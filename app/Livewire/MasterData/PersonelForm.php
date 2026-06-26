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
    public $deletingCompetencyIndex = null;

    // Track deleted IDs to exclude from wire:poll reload
    public $deletedCompetencyIds = [];

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

        // Load existing items from database, excluding deleted ones
        $existingItems = $personel->competencies
            ->reject(function ($competency) {
                return in_array($competency->pivot->id, $this->deletedCompetencyIds);
            })
            ->map(function ($competency) {
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
                    'issue_date' => $competency->pivot->issue_date,
                    'has_no_expiry' => (bool) $competency->pivot->has_no_expiry,
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
            'competencies.*.issue_date' => 'required|date',
            'competencies.*.has_no_expiry' => 'boolean',
            'competencies.*.expired_date' => 'nullable|date',
        ];

        // expired_date is required only if has_no_expiry is false
        foreach ($this->competencies as $index => $competency) {
            if (!isset($competency['has_no_expiry']) || !$competency['has_no_expiry']) {
                $rules["competencies.{$index}.expired_date"] = 'required|date';
            }
        }

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
            'competencies.*.issue_date' => 'tanggal terbit',
            'competencies.*.has_no_expiry' => 'sertifikat tidak punya tanggal expired',
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
            'issue_date' => null,
            'has_no_expiry' => false,
            'expired_date' => null,
        ];
    }

    public function removeCompetencyFile($index)
    {
        if (isset($this->competencies[$index])) {
            $this->competencies[$index]['certificate_file'] = null;
        }
    }

    public function removeCompetency($index)
    {
        $this->deletingCompetencyIndex = $index;
        $this->showDeleteCompetencyModal = true;
    }

    public function confirmDeleteCompetency()
    {
        if ($this->deletingCompetencyIndex !== null) {
            $item = $this->competencies[$this->deletingCompetencyIndex] ?? null;
            
            // If it's an existing record (numeric ID), track it for deletion on save
            if ($item && isset($item['id']) && is_numeric($item['id'])) {
                $this->deletedCompetencyIds[] = $item['id'];
            }
            
            unset($this->competencies[$this->deletingCompetencyIndex]);
            $this->competencies = array_values($this->competencies);
            $this->deletingCompetencyIndex = null;
            $this->showDeleteCompetencyModal = false;
        }
    }

    public function cancelDeleteCompetency()
    {
        $this->deletingCompetencyIndex = null;
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
        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $message = count($errors) > 1
                    ? 'Terdapat ' . count($errors) . ' kesalahan validasi'
                    : $errors[0];
                $this->dispatch('notify', type: 'error', message: $message);
            }
        })->validate();

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
                    'issue_date' => $competency['issue_date'] ?? null,
                    'has_no_expiry' => !empty($competency['has_no_expiry']),
                    'expired_date' => !empty($competency['has_no_expiry']) ? null : ($competency['expired_date'] ?? null),
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
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
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
