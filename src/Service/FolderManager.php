<?php

namespace Appolodev\ImageManager\Service;

use Appolodev\ImageManager\Service\Exception\CreateFolderException;
use Appolodev\ImageManager\Service\Exception\DeleteFolderException;
use Bolt\Configuration\FileLocations;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\PathUtil\Path;

class FolderManager
{
    private FileLocations $fileLocations;
    private TranslatorInterface $translator;

    public function __construct(FileLocations $fileLocations, TranslatorInterface $translator)
    {
        $this->fileLocations = $fileLocations;
        $this->translator = $translator;
    }

    public function listFolders(?string $baseFolder): array
    {
        $finder = new Finder();
        $finder->directories()->in($this->getAbsolutePath($baseFolder));

        if (!$finder->hasResults()) {
            return [];
        }

        $folders = [];
        foreach ($finder as $folder) {
            $folders[] = [
                'id' => $folder->getRelativePathname(),
                'name' => $folder->getFilename(),
                'parent' => '' === $folder->getRelativePath() ? null : $folder->getRelativePath(),
            ];
        }

        return $folders;
    }

    public function addFolder(?string $baseFolder, string $content): array
    {
        $baseFolderPath = $this->getAbsolutePath($baseFolder);

        $payload = json_decode($content, true);
        if (!is_array($payload) || !isset($payload['name']) || '' === $payload['name']) {
           throw new CreateFolderException($this->translator->trans('Une erreur est survenue lors de la création du dossier.'));  // TODO Phrase
        }

        $newFolderPath = $baseFolderPath . DIRECTORY_SEPARATOR . $payload['name'];

        if (array_key_exists('parent', $payload)) {
            if (!is_dir($baseFolderPath . DIRECTORY_SEPARATOR . $payload['parent'])) {
                throw new CreateFolderException($this->translator->trans('Le dossier parent n\'existe pas. Veuillez contacter un administrateur.'));  // TODO Phrase
            }

            $newFolderPath =  $baseFolderPath . DIRECTORY_SEPARATOR . $payload['parent'] . DIRECTORY_SEPARATOR . $payload['name'];
        }

        if (is_dir($newFolderPath)) {
            throw new CreateFolderException($this->translator->trans('Le dossier existe déjà.'));  // TODO Phrase
        }

        $filesystem = new Filesystem();
        $filesystem->mkdir($newFolderPath);

        return [
            'id' => $payload['parent'] ?? null . DIRECTORY_SEPARATOR . $payload['name'],
            'name' => $payload['name'],
            'parent' => $payload['parent'] ?? null,
        ];
    }

    public function deleteFolder(?string $baseFolder, string $folder): void
    {
        $folder = $this->getAbsolutePath($baseFolder) . DIRECTORY_SEPARATOR . $folder;

        if (!is_dir($folder)) {
            throw new DeleteFolderException($this->translator->trans('Le dossier n\'existe pas. Veuillez contacter un administrateur.'));  // TODO Phrase
        }

        $finder = new Finder();
        $finder->in($folder)->notContains('index.html');

        if ($finder->hasResults()) {
            throw new DeleteFolderException($this->translator->trans('Le dossier n\'est pas vide, il ne peut donc pas être supprimé. Veuillez contacter un administrateur.'));  // TODO Phrase
        }

        try {
            $filesystem = new Filesystem();
            $filesystem->remove([$folder]);
        } catch (IOException $e) {
            throw new DeleteFolderException($this->translator->trans('Une erreur est survenue. Veuillez contacter un administrateur.'));  // TODO Phrase
        }
    }

    public function getAbsolutePath(?string $baseFolder): string
    {
        $folder = $this->fileLocations->get('files');

        if (!is_null($baseFolder)) {
            $folder = Path::canonicalize($folder->getBasepath() . DIRECTORY_SEPARATOR . $baseFolder);

            if (!is_dir($folder)) {
                mkdir($folder);
            }
        }

        return $folder;
    }
}
