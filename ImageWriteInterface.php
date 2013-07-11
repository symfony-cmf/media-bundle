<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Write interface definition for ImageInterface.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface ImageWriteInterface extends ImageInterface, FileWriteInterface
{
    /**
     * Set image width in pixels
     *
     * @return integer $width
     */
    public function setWidth($width);

    /**
     * Set image height in pixels
     *
     * @return integer $height
     */
    public function setHeight($height);
}