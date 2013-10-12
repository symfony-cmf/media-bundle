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
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

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
}
