<?php

namespace Appolodev\ImageManager\Service;

use Appolodev\ImageManager\Service\Exception\CreateFileException;
use Appolodev\ImageManager\Service\Exception\DeleteFileException;
use Bolt\Configuration\FileLocations;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class FileManager
{
    private FileLocations $fileLocations;
    private TranslatorInterface $translator;
    private FolderManager $folderManager;

    public function __construct(FileLocations $fileLocations, TranslatorInterface $translator, FolderManager $folderManager)
    {
        $this->fileLocations = $fileLocations;
        $this->translator = $translator;
        $this->folderManager = $folderManager;
    }

    public function addFile(?string $baseFolder, ?string $folder, ?UploadedFile $file): array
    {
        $publicPath = $baseFolder;
        $filePath = $this->folderManager->getAbsolutePath($baseFolder);

        if (is_null($file)) {
            throw new CreateFileException($this->translator->trans('Le fichier n\'existe pas.'));  // TODO Phrase
        }

        if (!is_null($folder)) {
            $publicPath .= DIRECTORY_SEPARATOR.$folder;
            $filePath .= DIRECTORY_SEPARATOR.$folder;
        }

        if (!is_dir($filePath)) {
            throw new CreateFileException($this->translator->trans('Le dossier de destination n\'existe pas.'));  // TODO Phrase
        }

        $filename = $file->getClientOriginalName();
        if (file_exists($filePath.DIRECTORY_SEPARATOR.$filename)) {
            throw new CreateFileException($this->translator->trans('Un fichier existe déjà avec ce nom. Merci de le supprimer avant.'));  // TODO Phrase
        }

        $file->move($filePath, $filename);

        return $this->hydrateFile($file, $publicPath);
    }

    public function listFiles(?string $baseFolder, ?string $folder): array
    {
        $publicPath = $baseFolder;
        $finderPath = $this->folderManager->getAbsolutePath($baseFolder);

        $finder = new Finder();

        if (!is_null($folder)) {
            $finderPath .= DIRECTORY_SEPARATOR.$folder;
            $publicPath .= DIRECTORY_SEPARATOR.$folder;
        }

        $finder->files()->in($finderPath)->depth(0)->notContains('index.html');

        if (!$finder->hasResults()) {
            return [];
        }

        $files = [];
        foreach ($finder as $file) {
            $files[] = $this->hydrateFile($file, $publicPath);
        }

        return $files;
    }

    public function deleteFile(string $filePath): void
    {
        $folder = $this->fileLocations->get('files');
        $filePath = $folder->getBasepath().DIRECTORY_SEPARATOR.$filePath;

        if (!file_exists($filePath)) {
            throw new DeleteFileException('Le fichier n\'a pas pu été trouvé.');
        }

        unlink($filePath);
    }

    protected function hydrateFile(mixed $file, string $publicPath): array
    {
        $extension = '';
        if ($file instanceof \SplFileInfo) {
            $fileName = $file->getFilename();
            $extension = $file->getExtension();
        }
        if ($file instanceof UploadedFile) {
            $fileName = $file->getClientOriginalName();
            $extension = $file->getExtension();
        }

        if ($extension === '') {
            $extension = explode('.', $fileName);
            $extension = end($extension);
        }

        if (is_null($fileName)) {
            $fileName = uniqid();
        }

        $originalFileName = explode('___', $fileName);
        $originalFileName = end($originalFileName);

        $url = $publicPath . DIRECTORY_SEPARATOR . $fileName;

        return [
            'id' => $url,
            'name' => $originalFileName,
            'url' => $url,
            'size' => 0,
            'folder' => $publicPath,
            'thumbnail' => '/files/'.$url,
        ];
    }
}
