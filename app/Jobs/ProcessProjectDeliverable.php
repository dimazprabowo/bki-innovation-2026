<?php

namespace App\Jobs;

use App\Models\ProjectDeliverable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessProjectDeliverable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public int $deliverableId,
        public string $tempPath,
        public string $fileName
    ) {}

    public function handle(): void
    {
        try {
            $deliverable = ProjectDeliverable::find($this->deliverableId);

            if (!$deliverable) {
                return;
            }

            if (!Storage::disk('local')->exists($this->tempPath)) {
                $deliverable->update([
                    'file_status' => 'failed',
                    'file_error' => 'Temporary file not found',
                ]);
                return;
            }

            $fileContent = Storage::disk('local')->get($this->tempPath);
            $fileSize = strlen($fileContent);

            $extension = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
            $finalFileName = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = 'project-deliverables/' . $deliverable->project_id . '/' . $finalFileName;

            Storage::disk('local')->put($destinationPath, $fileContent);
            Storage::disk('local')->delete($this->tempPath);

            $deliverable->update([
                'file_path' => $destinationPath,
                'file_name' => $this->fileName,
                'file_size' => $fileSize,
                'file_status' => 'completed',
                'file_processed_at' => now(),
                'file_error' => null,
            ]);
        } catch (\Exception $e) {
            if (isset($deliverable)) {
                $deliverable->update([
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
        $deliverable = ProjectDeliverable::find($this->deliverableId);

        if ($deliverable) {
            $deliverable->update([
                'file_status' => 'failed',
                'file_error' => $exception->getMessage(),
            ]);
        }

        if (Storage::disk('local')->exists($this->tempPath)) {
            Storage::disk('local')->delete($this->tempPath);
        }
    }
}
