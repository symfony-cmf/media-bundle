<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for image container objects. This just adds methods to get the
 * native image dimensions, but implicitly also tells applications that this
 * object is suitable to view with an <img> HTML tag.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface ImageInterface extends FileInterface
{
    /**
     * Get image width in pixels.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get image height in pixels.
     *
     * @return int
     */
    public function getHeight();
}
