<?php

namespace App\Jobs;

use App\Models\Personel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessPersonelCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public int $personelId,
        public int $competencyId,
        public string $tempPath,
        public string $fileName
    ) {}

    public function handle(): void
    {
        try {
            if (!Storage::disk('local')->exists($this->tempPath)) {
                DB::table('personel_competency')
                    ->where('personel_id', $this->personelId)
                    ->where('competency_id', $this->competencyId)
                    ->update([
                        'certificate_file_status' => 'failed',
                        'certificate_file_error' => 'Temporary file not found',
                    ]);
                return;
            }

            $fileContent = Storage::disk('local')->get($this->tempPath);
            $fileSize = strlen($fileContent);

            $extension = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
            $finalFileName = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = 'personel-certificates/' . $finalFileName;

            Storage::disk(file_disk())->put($destinationPath, $fileContent);
            Storage::disk('local')->delete($this->tempPath);

            DB::table('personel_competency')
                ->where('personel_id', $this->personelId)
                ->where('competency_id', $this->competencyId)
                ->update([
                    'certificate_file_path' => $destinationPath,
                    'certificate_file_name' => $this->fileName,
                    'certificate_file_size' => $fileSize,
                    'certificate_file_status' => 'completed',
                    'certificate_file_processed_at' => now(),
                    'certificate_file_error' => null,
                ]);
        } catch (\Exception $e) {
            DB::table('personel_competency')
                ->where('personel_id', $this->personelId)
                ->where('competency_id', $this->competencyId)
                ->update([
                    'certificate_file_status' => 'failed',
                    'certificate_file_error' => $e->getMessage(),
                ]);

            if (Storage::disk('local')->exists($this->tempPath)) {
                Storage::disk('local')->delete($this->tempPath);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        DB::table('personel_competency')
            ->where('personel_id', $this->personelId)
            ->where('competency_id', $this->competencyId)
            ->update([
                'certificate_file_status' => 'failed',
                'certificate_file_error' => $exception->getMessage(),
            ]);

        if (Storage::disk('local')->exists($this->tempPath)) {
            Storage::disk('local')->delete($this->tempPath);
        }
    }
}
