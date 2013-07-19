<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * A basic interface for media objects. Be they cloud hosted or local files.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface MediaMetaDataInterface extends MediaInterface
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
     * @return void
     */
    public function setDescription($description);

    /**
     * The copyright text, e.g. a license name
     *
     * @return string
     */
    public function getCopyright();

    /**
     * @param string $copyright
     * @return void
     */
    public function setCopyright($copyright);

    /**
     * The name of the author of the media represented by this object
     *
     * @return string
     */
    public function getAuthorName();

    /**
     * @param string $author
     * @return void
     */
    public function setAuthorName($author);

    /**
     * Get all metadata
     *
     * @return array
     */
    public function getMetadata();

    /**
     * @param string $name
     * @param null   $default to be used if $name is not set in the metadata
     */
    public function getMetadataValue($name, $default = null);
}
