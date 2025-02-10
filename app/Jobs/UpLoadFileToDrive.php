<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GoogleDriveService;


class UpLoadFileToDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $publicUrl;
    protected $fileName;

    public function __construct($publicUrl, $fileName)
    {
        $this->publicUrl = $publicUrl;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        $googleDriveService = new GoogleDriveService();
        $googleDriveService->uploadFile($this->publicUrl, $this->fileName);
    }
}
