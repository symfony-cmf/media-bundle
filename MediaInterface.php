<?php

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
     * Get the unique identifier of this media element
     *
     * @return string
     */
    public function getId();

    /**
     * The name of this media, e.g. for managing media documents
     *
     * For example an image file name.
     *
     * @return string
     */
    public function getName();

    /**
     * The caption to show to users
     *
     * @return string
     */
    public function getCaption();

    /**
     * The copyright text, e.g. a license name
     *
     * @return string
     */
    public function getCopyright();

    /**
     * The name of the author of the media represented by this object
     *
     * @return string
     */
    public function getAuthorName();

    /**
     * @param string $name
     * @param null   $default to be used if $name is not set in the metadata
     */
    public function getMetadataValue($name, $default = null);

    /**
     * Get creation date
     *
     * @return \Datetime $createdAt
     */
    public function getCreatedAt();

    /**
     * Get last update date
     *
     * @return \Datetime $updatedAt
     */
    public function getUpdatedAt();
}