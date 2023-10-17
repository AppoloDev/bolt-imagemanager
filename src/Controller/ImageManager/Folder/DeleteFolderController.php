<?php

namespace Appolodev\ImageManager\Controller\ImageManager\Folder;

use Appolodev\ImageManager\Service\Exception\DeleteFolderException;
use Appolodev\ImageManager\Service\FolderManager;
use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteFolderController  extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint/folders/{id}", name="image_manager_delete_folder", requirements={"id"=".+"}, methods={"DELETE"})
     */
    public function __invoke(Request $request, FolderManager $folderManager, string $id): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');

        $baseFolder = is_string($request->query->get('baseFolder')) ? $request->query->get('baseFolder') : null;

        try {
            $folderManager->deleteFolder($baseFolder, $id);
            return $this->json([], 201);
        } catch (DeleteFolderException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
