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
 * A basic interface for media objects. Be they cloud hosted or local files.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface MediaInterface
{
    /**
     * Get the unique identifier of this media element.
     *
     * @return string
     */
    public function getId();

    /**
     * The name of this media, e.g. for managing media documents.
     *
     * For example an image file name.
     *
     * @return string
     */
    public function getName();

    /**
     * @param $name
     */
    public function setName($name);

    /**
     * Get creation date.
     *
     * @return \Datetime
     */
    public function getCreatedAt();

    /**
     * Get last update date.
     *
     * @return \Datetime
     */
    public function getUpdatedAt();
}
