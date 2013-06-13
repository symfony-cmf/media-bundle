<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

/**
 * TODO: create and add cmf:image mixin
 * This class represents a CmfMedia Doctrine PHPCR image.
 */
class Image extends File implements ImageInterface
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * {@inheritDoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * {@inheritDoc}
     */
    protected function updateDimensionsFromContent()
    {
        parent::updateDimensionsFromContent();

        $resource = imagecreatefromstring($this->getContentAsString());
        $this->setWidth(imagesx($resource));
        $this->setHeight(imagesy($resource));
    }
}
