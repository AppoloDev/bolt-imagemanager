<?php

namespace Appolodev\ImageManager\Controller\ImageManager\File;

use Appolodev\ImageManager\Service\Exception\DeleteFileException;
use Appolodev\ImageManager\Service\FileManager;
use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteFileController  extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint/files/{filePath}", name="image_manager_delete_file", requirements={"filePath"=".+"}, methods={"DELETE"})
     */
    public function __invoke(Request $request, FileManager $fileManager, string $filePath): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');

        try {
            $fileManager->deleteFile($filePath);
            return $this->json([], 204);
        } catch (DeleteFileException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
