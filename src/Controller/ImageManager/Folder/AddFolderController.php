<?php

namespace Appolodev\ImageManager\Controller\ImageManager\Folder;

use Appolodev\ImageManager\Service\Exception\CreateFolderException;
use Appolodev\ImageManager\Service\FolderManager;
use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddFolderController  extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint/folders", name="image_manager_add_folder", methods={"POST"})
     */
    public function __invoke(Request $request, FolderManager $folderManager): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');

        $baseFolder = is_string($request->query->get('baseFolder')) ? $request->query->get('baseFolder') : null;

        try {
            $folder = $folderManager->addFolder($baseFolder, (string)$request->getContent());
            return $this->json($folder, 201);
        } catch (CreateFolderException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
