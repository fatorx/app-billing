<?php

namespace Billing\Storage;

use Billing\Values\PostFile;
use Exception;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use SplFileObject;
use ZipArchive;

class StorageFile
{
    const MESSAGE_FAIL_SEND_FILE = 'Não foi possível armazenar o arquivo.';
    const MESSAGE_FAIL_GET_FILE = 'Não foi possível recuperar o arquivo.';
    const MESSAGE_FAIL_GET_FILE_NAME = 'Não foi possível armazenar o arquivo nomeado.';

    private string $uuidStorage;
    private string $path;


    public function __construct($uuidStorage = '')
    {
        $this->uuidStorage = $uuidStorage;
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

        $filePath = $this->upload($tmpName);
        $this->zipFile($filePath);

        return true;
    }

    /**
     * @param $tmpName
     * @return string
     * @throws Exception
     */
    public function upload($tmpName): string
    {
        $filePath = $this->getFilePath($this->uuidStorage, 'csv');
        $isSendFile = $this->uploadFile($tmpName, $filePath);
        if (!$isSendFile) {
            throw new Exception(self::MESSAGE_FAIL_SEND_FILE);
        }
        return $filePath;
    }

    /**
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    public function zipFile(string $filePath): void
    {
        $entryName = "{$this->uuidStorage}.csv";
        $pathCsv = $this->path . $entryName;
        if (!is_file($pathCsv)) {
            throw new Exception(self::MESSAGE_FAIL_SEND_FILE);
        }

        $fileZipPath = $this->getFilePath($this->uuidStorage, 'zip');
        $zip = new ZipArchive();
        if ($zip->open($fileZipPath, ZipArchive::CREATE) !== true) {
            throw new Exception(self::MESSAGE_FAIL_GET_FILE_NAME);
        }

        $entryName = "/{$this->uuidStorage}.csv";
        $zip->addFile($filePath, $entryName);

        $zip->close();

        unlink($filePath);
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

    /**
     * @throws Exception
     */
    public function get(string $uuid, $ext = 'zip'): array
    {
        $list = [];
        try {
            $pathFile = $this->path = getcwd() . "/data/storage/{$uuid}.{$ext}" ;
            $fileObject = new SplFileObject($pathFile);

            if ($fileObject->isFile()) {
                $this->extracted($pathFile);

                $pathTmp = "/tmp/{$uuid}.csv";
                $fileExtract = new SplFileObject($pathTmp);

                while (!$fileExtract->eof()) {
                    $list[] = $fileExtract->fgetcsv();
                }
            }
        } catch(RuntimeException $e) {
            echo $e->getMessage();
        }

        return $list;
    }

    /**
     * @param string $pathFile
     * @return void
     * @throws Exception
     */
    public function extracted(string $pathFile): void
    {
        $zip = new ZipArchive();
        if ($zip->open($pathFile) !== true) {
            throw new Exception(self::MESSAGE_FAIL_GET_FILE);
        }

        $zip->extractTo('/tmp');
        $zip->close();
    }




}
