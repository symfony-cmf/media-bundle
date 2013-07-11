<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Write interface definition for MediaInterface.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface MediaWriteInterface extends MediaInterface
{
    /**
     * The name of this media, e.g. for managing media documents
     *
     * For example an image file name.
     *
     * @return string $name
     */
    public function setName($name);

    /**
     * The description to show to users, e.g. an image caption or some text
     * to put after the filename.
     *
     * @return string $description
     */
    public function setDescription($description);

    /**
     * The copyright text, e.g. a license name
     *
     * @return string $copyright
     */
    public function setCopyright($copyright);

    /**
     * The name of the author of the media represented by this object
     *
     * @return string $authorName
     */
    public function setAuthorName($authorName);

    /**
     * Set all metadata
     *
     * @return array $metadata
     */
    public function setMetadata(array $metadata);

    /**
     * The metadata value
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setMetadataValue($name, $value);

    /**
     * Remove a named data from the metadata
     *
     * @param string $name
     */
    public function unsetMetadataValue($name);
}