<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

/**
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
     * {@inheritdoc}
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * {@inheritdoc}
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
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Determine the with and height of the object from
     * the binary image data
     */
    protected function updateDimensionsFromContent()
    {
        parent::updateDimensionsFromContent();

        $content = $this->getContentAsString();

        if (is_string($content) && strlen($content) > 0) {
            $resource = imagecreatefromstring($content);
            $this->setWidth(imagesx($resource));
            $this->setHeight(imagesy($resource));
        } else {
            $this->setWidth(0);
            $this->setHeight(0);
        }
    }
}
