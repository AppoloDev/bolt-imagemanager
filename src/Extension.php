<?php

namespace Appolodev\ImageManager;

use Appolodev\ImageManager\Widget\ImageManagerInjectorWidget;
use Bolt\Extension\BaseExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;

class Extension extends BaseExtension
{
    public function getName(): string
    {
        return 'Appolodev - ImageManager';
    }

    public function initialize(): void
    {
        $this->addTwigNamespace('imagemanager');
        // This Injector Widget is used to insert CSS and JS for a field type
        // Therefore it is only inserted once even if you have multiple fields of this field type
        $this->addWidget(new ImageManagerInjectorWidget());
    }

    /**
     * This function will copy all the files from /assets/ into the
     * /public/<extension-name>/ folder after it has been installed.
     *
     * If the user defines a different public directory the assets will
     * be copied to the custom public directory
     */
    public function install(): void
    {
        /** @var Container $container */
        $container = $this->getContainer();
        $projectDir = $container->getParameter('kernel.project_dir');
        $public = $container->getParameter('bolt.public_folder');

        $source = \dirname(__DIR__) . '/build/';
        $destination = $projectDir . '/' . $public . '/assets/';

        $filesystem = new Filesystem();
        $filesystem->mirror($source, $destination);
    }
}
