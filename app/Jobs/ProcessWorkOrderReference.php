<?php

namespace App\Jobs;

use App\Models\WorkOrderReference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessWorkOrderReference implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public int $referenceId,
        public string $tempPath,
        public string $fileName
    ) {}

    public function handle(): void
    {
        try {
            $reference = WorkOrderReference::find($this->referenceId);
            
            if (!$reference) {
                return;
            }

            if (!Storage::disk('local')->exists($this->tempPath)) {
                $reference->update([
                    'file_status' => 'failed',
                    'file_error' => 'Temporary file not found',
                ]);
                return;
            }

            $fileContent = Storage::disk('local')->get($this->tempPath);
            $fileSize = strlen($fileContent);

            $extension = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
            $finalFileName = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = 'work-order-references/' . $finalFileName;

            Storage::disk(file_disk())->put($destinationPath, $fileContent);
            Storage::disk('local')->delete($this->tempPath);

            $reference->update([
                'file_path' => $destinationPath,
                'file_name' => $this->fileName,
                'file_size' => $fileSize,
                'file_status' => 'completed',
                'file_processed_at' => now(),
                'file_error' => null,
            ]);
        } catch (\Exception $e) {
            if (isset($reference)) {
                $reference->update([
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
        $reference = WorkOrderReference::find($this->referenceId);
        
        if ($reference) {
            $reference->update([
                'file_status' => 'failed',
                'file_error' => $exception->getMessage(),
            ]);
        }

        if (Storage::disk('local')->exists($this->tempPath)) {
            Storage::disk('local')->delete($this->tempPath);
        }
    }
}
