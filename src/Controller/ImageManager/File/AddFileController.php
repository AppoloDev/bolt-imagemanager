<?php

namespace Appolodev\ImageManager\Controller\ImageManager\File;

use Appolodev\ImageManager\Service\Exception\CreateFileException;
use Appolodev\ImageManager\Service\FileManager;
use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddFileController  extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint/files", name="image_manager_add_file", methods={"POST"})
     */
    public function __invoke(Request $request, FileManager $fileManager): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');

        $baseFolder = is_string($request->query->get('baseFolder')) ? $request->query->get('baseFolder') : null;

        $folder = is_string($request->request->get('folder')) ? $request->request->get('folder') : null;

        /** @var ?UploadedFile $file */
        $file = $request->files->get('file') instanceof UploadedFile ? $request->files->get('file') : null;

        try {
            $file = $fileManager->addFile($baseFolder, $folder, $file);
            return $this->json($file, 201);
        } catch (CreateFileException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
