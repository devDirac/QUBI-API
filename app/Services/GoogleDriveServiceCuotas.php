<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Http\MediaFileUpload;
use GuzzleHttp\Psr7\Request;

class GoogleDriveServiceCuotas
{
  protected $client;
  protected $driveService;

  public function __construct()
  {
    $this->client = new Client();
    $this->client->setAuthConfig('C:/xampp/htdocs/APM/storage/app/credentials/secret_drive.json');
    $this->client->addScope(Drive::DRIVE_FILE);

    $this->driveService = new Drive($this->client);
  }

  public function uploadLargeFile($filePath, $fileName)
  {
    $fileSize = filesize($filePath);
    $chunkSize = 1 * 1024 * 1024; // Tamaño del fragmento (256 KB)

    // Verificar y obtener el token de acceso
    $accessToken = $this->client->getAccessToken();

    if (!$accessToken || !isset($accessToken['access_token'])) {
      // Forzar la generación de un nuevo token si no está disponible
      $accessToken = $this->client->fetchAccessTokenWithAssertion();

      if (!$accessToken || !isset($accessToken['access_token'])) {
        $errorMessage = isset($accessToken['error']) ? json_encode($accessToken) : 'No se pudo obtener un token de acceso válido.';
        throw new \Exception($errorMessage);
      }
    }

   //| var_dump('Token de acceso: ' . $accessToken['access_token']);

    $token = $accessToken['access_token'];

    $fileMetadata = [
      'name' => $fileName,
      'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')], // Reemplaza con el ID de tu carpeta
    ];

    // Crear solicitud inicial de subida resumible
    $response = $this->client->getHttpClient()->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable', [
      'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json; charset=UTF-8',
      ],
      'body' => json_encode($fileMetadata),
    ]);
    //var_dump($response);
    // Obtener la URL de subida desde el encabezado 'Location'
    /*  $uploadUrl_ = $response->getHeader('Location')[0] ?? null;
     var_dump(value: $uploadUrl_);

     $headers = $response->getHeaders();
     var_dump(value: $headers);

     var_dump(value: $headers['Location']); */



    $uploadUrl = $response->getHeader('Location')[0] ?? null;




    if (!$uploadUrl) {
      $errorResponse = (string) $response->getBody();
      throw new \Exception('No se pudo obtener la URL de subida resumible. Respuesta: ' . $errorResponse);
    }

    // Agrega este log para verificar la URL resumible
    //var_dump('URL de subida resumible: ' . $uploadUrl);

    $request = new Request(
      'PUT', // Método HTTP correcto para subir archivos a Drive
      $uploadUrl,
      [
        'Content-Type' => mime_content_type($filePath), // Tipo MIME del archivo
        'Authorization' => 'Bearer ' . $token,
      ]
    );

    // Configurar MediaFileUpload
    $media = new MediaFileUpload(
      $this->client,
      $request, // Se pasa la URL directamente
      mime_content_type($filePath),
      null,
      true,
      $chunkSize
    );
    $media->setFileSize($fileSize);

    // Subir el archivo en fragmentos
    $handle = fopen($filePath, 'rb');
    if ($handle === false) {
      throw new \Exception('No se pudo abrir el archivo: ' . $filePath);
    }

    $status = false;
    while (!$status && !feof($handle)) {
      $chunk = fread($handle, $chunkSize);
      var_dump('Tamaño del fragmento leído: ' . strlen($chunk));
      try {
        $status = $media->nextChunk($chunk);
       // var_dump('Fragmento subido exitosamente.');
      } catch (\Exception $e) {
        fclose($handle);
        throw new \Exception('Error durante la subida del archivo: ' . $e->getMessage());
      }
    }

    fclose($handle);

    if ($status !== false) {
      return $status->id;
    }

    throw new \Exception('Error durante la subida del archivo.');



    // Crear la solicitud HTTP (RequestInterface)
    /*  $request = new Request(
       'POST',
       $uploadUrl,
       [
         'Authorization' => 'Bearer ' . $token,
         'Content-Type' => 'application/json; charset=UTF-8',
       ],
       json_encode($fileMetadata)
     ); */

    // Configurar MediaFileUpload
    // $media = new MediaFileUpload(
    //  $this->client,
    // $request,
    // mime_content_type(/* $filePath */ 'C:\xampp\htdocs\APM\storage\app\documentos\39\869\estimaciones\244\estimacion_244.zip'),
    // null,
    // true,
    // $chunkSize
    //);
    //$media->setFileSize($fileSize);



    // Subir el archivo en fragmentos
    // $handle = fopen(/* $filePath */ 'C:\xampp\htdocs\APM\storage\app\documentos\39\869\estimaciones\244\estimacion_244.zip', 'rb');
    //  if ($handle === false) {
    //    throw new \Exception('No se pudo abrir el archivo: ' . /* $filePath */ 'C:\xampp\htdocs\APM\storage\app\documentos\39\869\estimaciones\244\estimacion_244.zip');
    // }

    // $status = false;
    //  while (!$status && !feof($handle)) {
    //    $chunk = fread($handle, $chunkSize);
    ///    $status = $media->nextChunk($chunk);
    //  }

    //  fclose($handle);

    // if ($status !== false) {
    //    return $status->id;
    //  }

    //   throw new \Exception('Error durante la subida del archivo.');

  }
}