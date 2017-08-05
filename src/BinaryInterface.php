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
 * Interface for objects containing a binary file embedded.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface BinaryInterface extends FileInterface
{
    /**
     * Get a php stream with the data of this file.
     *
     * @return stream
     */
    public function getContentAsStream();

    /**
     * @param $stream
     */
    public function setContentFromStream($stream);
}
