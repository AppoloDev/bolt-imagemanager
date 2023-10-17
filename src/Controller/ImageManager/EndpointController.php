<?php

namespace Appolodev\ImageManager\Controller\ImageManager;

use Bolt\Controller\Backend\BackendZoneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EndpointController extends AbstractController implements BackendZoneInterface
{
    /**
     * @Route("/image-manager/endpoint", name="image_manager_endpoint")
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('managefiles:files');
        return $this->json([]);
    }
}
