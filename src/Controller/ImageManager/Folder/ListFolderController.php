<?php

namespace Appolodev\ImageManager\Controller\ImageManager\Folder;

use Appolodev\ImageManager\Service\FolderManager;
use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListFolderController  extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint/folders", name="image_manager_get_folders", methods={"GET"})
     */
    public function __invoke(Request $request, FolderManager $folderManager): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');

        $baseFolder = is_string($request->query->get('baseFolder')) ? $request->query->get('baseFolder') : null;
        $folders = $folderManager->listFolders($baseFolder);

        return $this->json($folders);
    }
}
