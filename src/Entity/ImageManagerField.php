<?php

declare(strict_types=1);

namespace Appolodev\ImageManager\Entity;

use Bolt\Entity\Field;
use Bolt\Entity\Field\Excerptable;
use Bolt\Entity\FieldInterface;
use Bolt\Utils\ThumbnailHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Twig\Markup;

/**
 * @ORM\Entity
 */
class ImageManagerField extends Field implements Excerptable, FieldInterface
{
    public const TYPE = 'imagemanager';

    private function getFieldBase()
    {
        return [
            'filename' => '',
            'alt' => '',
        ];
    }

    public function __toString(): string
    {
        return (string) $this->getPath();
    }

    public function getValue(): array
    {
        $value = array_merge($this->getFieldBase(), (array) parent::getValue() ?: []);

        // Remove cruft `0` field getting stored as JSON.
        unset($value[0]);

        $value['fieldname'] = $this->getName();

        // If the filename isn't set, we're done: return the array with placeholders
        if (! $value['filename']) {
            return $value;
        }

        return $value;
    }

    /**
     * Override getTwigValue to render field as html
     */
    public function getTwigValue()
    {
        $value = parent::getTwigValue();

        return new Markup($value, 'UTF-8');
    }
}
