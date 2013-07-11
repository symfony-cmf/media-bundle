<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for objects containing a file.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface FileInterface extends MediaInterface
{
    /**
     * Returns the content
     *
     * @return string
     */
    public function getContentAsString();

    /**
     * Get the file size in bytes
     *
     * @return integer
     */
    public function getSize();

    /**
     * The mime type of this media element
     *
     * @return string
     */
    public function getContentType();

    /**
     * Get the default file name extension for files of this format
     *
     * @return string
     */
    public function getExtension();
}