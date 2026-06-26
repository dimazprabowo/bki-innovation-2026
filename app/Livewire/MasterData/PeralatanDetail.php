<?php

namespace App\Livewire\MasterData;

use App\Livewire\Traits\HasNotification;
use App\Models\Peralatan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PeralatanDetail extends Component
{
    use HasNotification;

    public $peralatanId;

    public function mount(Peralatan $peralatan)
    {
        $this->peralatanId = $peralatan->id;
    }

    public function goBack()
    {
        return $this->redirect(route('master-data.peralatan.index'), navigate: true);
    }

    public function downloadEvidence($evidenceId)
    {
        $peralatan = Peralatan::with('evidences')->findOrFail($this->peralatanId);
        $evidence = $peralatan->evidences->find($evidenceId);

        if (!$evidence) {
            $this->notifyError('Evidence tidak ditemukan.');
            return;
        }

        if (!$evidence->file_path) {
            $this->notifyError('File belum diunggah.');
            return;
        }

        if (!Storage::disk(file_disk())->exists($evidence->file_path)) {
            $this->notifyError('File tidak ditemukan di penyimpanan.');
            return;
        }

        $peralatanName = $peralatan->code;
        $evidenceName = $evidence->name ?? 'evidence';
        $originalExtension = pathinfo($evidence->file_name, PATHINFO_EXTENSION);
        $downloadName = "{$peralatanName}_{$evidenceName}.{$originalExtension}";

        return Storage::disk(file_disk())->download($evidence->file_path, $downloadName);
    }

    public function render()
    {
        $peralatan = Peralatan::with([
            'evidences',
            'reviewer',
        ])->findOrFail($this->peralatanId);

        return view('livewire.master-data.peralatan-detail', [
            'peralatan' => $peralatan,
        ]);
    }
}
