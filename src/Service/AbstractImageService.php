<?php
namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractImageService
{
    protected string $uploadDirectory;
    protected SluggerInterface $slugger;

    public function __construct(
        string $uploadDirectory,
        SluggerInterface $slugger
    ) {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Upload a single image and return the filename
     * @throws Exception
     */
    protected function uploadImage(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            // Ensure target directory exists
            if (!is_dir($this->uploadDirectory)) {
                mkdir($this->uploadDirectory, 0755, true);
            }

            $file->move($this->uploadDirectory, $fileName);
            return $fileName;
        } catch (FileException $e) {
            throw new Exception('Error uploading file: ' . $e->getMessage());
        }
    }

    /**
     * Remove a file from the filesystem
     */
    protected function removeFile(string $fileName): void
    {
        $filePath = $this->uploadDirectory . '/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
