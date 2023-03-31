<?php

namespace Billing\Storage;

use Billing\Values\PostFile;
use Exception;
use Ramsey\Uuid\Uuid;
use ZipArchive;

class StorageFile
{
    const MESSAGE_FAIL_SEND_FILE = 'Não foi possível armazenar o arquivo.';

    private string $path;
    private string $uuidStorage;

    public function __construct()
    {
        $this->path = getcwd() . '/data/storage/';
    }

    /**
     * @throws Exception
     */
    public function persist(PostFile $postFile): bool
    {
        $data = $postFile->getData();
        $tmpName = $data['file']['tmp_name'];

        $uuid4 = Uuid::uuid4();
        $this->uuidStorage = $uuid4->toString();

        $filePath = $this->getFilePath($this->uuidStorage, 'csv');
        $isSendFile = $this->uploadFile($tmpName, $filePath);
        if (!$isSendFile) {
            throw new Exception(self::MESSAGE_FAIL_SEND_FILE);
        }

        $fileZipPath = $this->getFilePath($this->uuidStorage, 'zip');
        $zip = new ZipArchive();
        if ($zip->open($fileZipPath, ZipArchive::CREATE) !== true) {
            throw new Exception(self::MESSAGE_FAIL_SEND_FILE);
        }

        $entryName = "/{$this->uuidStorage}.csv";
        $zip->addFile($filePath,$entryName);

        $zip->close();

        return true;
    }

    public function uploadFile($tmpName, $filePath): bool
    {
        return copy($tmpName, $filePath);
    }

    public function getFilePath(string $uuid, $ext): string
    {
        $fileName = $uuid . "." . $ext;

        return $this->path . $fileName;
    }

    public function getUuidStorage(): string
    {
        return $this->uuidStorage;
    }
}
