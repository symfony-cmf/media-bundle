<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for objects containing a file.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface FileInterface extends HierarchyInterface, MediaMetaDataInterface
{
    /**
     * Returns the content
     *
     * @return string
     */
    public function getContentAsString();

    /**
     * Set the content
     *
     * @param string $content
     *
     * @return boolean
     */
    public function setContentFromString($content);

    /**
     * Copy the content from a file, this allows to optimize copying the data
     * of a file. It is preferred to use the dedicated content setters if
     * possible.
     *
     * @param FileInterface|\SplFileInfo $file
     *
     * @throws \InvalidArgumentException if file is no FileInterface|\SplFileInfo
     */
    public function copyContentFromFile($file);

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

    /**
     * Get the file size in bytes
     *
     * @return integer
     */
    public function getSize();

}
