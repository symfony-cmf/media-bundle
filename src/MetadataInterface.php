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
interface MetadataInterface extends MediaInterface
{
    /**
     * The description to show to users, e.g. an image caption or some text
     * to put after the filename.
     *
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * The copyright text, e.g. a license name.
     *
     * @return string
     */
    public function getCopyright();

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright);

    /**
     * The name of the author of the media represented by this object.
     *
     * @return string
     */
    public function getAuthorName();

    /**
     * @param string $author
     */
    public function setAuthorName($author);

    /**
     * Get all metadata.
     *
     * @return array
     */
    public function getMetadata();

    /**
     * Set all metadata.
     *
     * @param array $metadata
     *
     * @return mixed
     */
    public function setMetadata(array $metadata);

    /**
     * @param string $name
     * @param string $default to be used if $name is not set in the metadata
     *
     * @return string
     */
    public function getMetadataValue($name, $default = null);

    /**
     * The metadata value.
     *
     * @param string $name
     * @param string $value
     */
    public function setMetadataValue($name, $value);

    /**
     * Remove a named data from the metadata.
     *
     * @param string $name
     */
    public function unsetMetadataValue($name);
}
