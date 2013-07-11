<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Write interface definition for FileInterface.
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface FileWriteInterface extends FileInterface, MediaWriteInterface
{
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
     * Set the file size in bytes
     *
     * @return integer
     */
    public function setSize($size);

    /**
     * Set the mime type of this media element
     *
     * @return string
     */
    public function setContentType($contentType);
}