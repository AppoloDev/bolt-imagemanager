<?php

namespace Appolodev\ImageManager\Controller\ImageManager\File;

use Appolodev\ImageManager\Service\FileManager;
use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListFileController  extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint/files", name="image_manager_get_files", methods={"GET"})
     */
    public function __invoke(Request $request, FileManager $fileManager): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');

        $baseFolder = is_string($request->query->get('baseFolder')) ? $request->query->get('baseFolder') : null;
        $folder = is_string($request->query->get('folder')) ? $request->query->get('folder') : null;
        $files = $fileManager->listFiles($baseFolder, $folder);

        return $this->json($files);
    }


}
