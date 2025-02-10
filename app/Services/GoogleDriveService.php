<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Http\MediaFileUpload;

class GoogleDriveService
{
    protected $client;
    protected $driveService;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(__DIR__ .'/secret_drive.json');
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->driveService = new Drive($this->client);
    }

    public function uploadFile($filePath, $fileName)
    {
        $fileMetadata = new Drive\DriveFile([
          'name' => $fileName,
          'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
        ]);
        $content = file_get_contents( $filePath );
        $file = $this->driveService->files->create($fileMetadata, [
          'data' => $content,
          'mimeType' => mime_content_type( $filePath ),
          'uploadType' => 'multipart',
        ]);
        return $file->id;
    }

}