<?php

declare(strict_types=1);

namespace Appolodev\ImageManager\Widget;

use Bolt\Widget\BaseWidget;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\TwigAwareInterface;
use Symfony\Component\HttpFoundation\Request;

class ImageManagerInjectorWidget extends BaseWidget implements TwigAwareInterface
{
    protected $name = 'Image Manager Injector Widget';
    protected $target = Target::AFTER_JS;
    protected $zone = RequestZone::BACKEND;
    protected $template = '@imagemanager/injector.html.twig';
    protected $priority = 200;

    public function run(array $params = []): ?string
    {
        /** @var Request $request */
        $request = $this->getExtension()->getRequest();

        if (! \in_array($request->get('_route'), ['bolt_content_edit', 'bolt_content_new', 'bolt_content_duplicate'], true) ||
            ($this->getExtension()->getRequest()->getMethod() !== 'GET')) {
            return null;
        }

        return parent::run();
    }
}
