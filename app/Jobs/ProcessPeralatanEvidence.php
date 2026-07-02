<?php

namespace App\Jobs;

use App\Models\PeralatanEvidence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessPeralatanEvidence implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public int $evidenceId,
        public string $tempPath,
        public string $fileName
    ) {}

    public function handle(): void
    {
        try {
            $evidence = PeralatanEvidence::find($this->evidenceId);
            
            if (!$evidence) {
                return;
            }

            if (!Storage::disk('local')->exists($this->tempPath)) {
                $evidence->update([
                    'file_status' => 'failed',
                    'file_error' => 'Temporary file not found',
                ]);
                return;
            }

            $fileContent = Storage::disk('local')->get($this->tempPath);
            $fileSize = strlen($fileContent);

            $extension = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
            $peralatanSlug = $evidence->peralatan ? Str::slug($evidence->peralatan->name) : 'unknown-peralatan';
            $evidenceSlug = Str::slug($evidence->name ?? 'evidence');
            $timestamp = now()->format('YmdHis');
            $finalFileName = $evidenceSlug . '_' . $timestamp . '.' . $extension;
            $destinationPath = config('app.env') . '/peralatan-evidence/' . $peralatanSlug . '/' . $finalFileName;

            $disk = file_disk();
            Storage::disk($disk)->put($destinationPath, $fileContent);

            if (!Storage::disk($disk)->exists($destinationPath)) {
                throw new \RuntimeException('Failed to upload file to storage: file does not exist after put()');
            }

            Storage::disk('local')->delete($this->tempPath);

            $evidence->update([
                'file_path' => $destinationPath,
                'file_name' => $this->fileName,
                'file_size' => $fileSize,
                'file_status' => 'completed',
                'file_processed_at' => now(),
                'file_error' => null,
            ]);
        } catch (\Exception $e) {
            if (isset($evidence)) {
                $evidence->update([
                    'file_status' => 'failed',
                    'file_error' => $e->getMessage(),
                ]);
            }

            if (Storage::disk('local')->exists($this->tempPath)) {
                Storage::disk('local')->delete($this->tempPath);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $evidence = PeralatanEvidence::find($this->evidenceId);
        
        if ($evidence) {
            $evidence->update([
                'file_status' => 'failed',
                'file_error' => $exception->getMessage(),
            ]);
        }

        if (Storage::disk('local')->exists($this->tempPath)) {
            Storage::disk('local')->delete($this->tempPath);
        }
    }
}
