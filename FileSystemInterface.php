<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for objects containing a file stored on the filesystem.
 *
 * For ORM you could use:
 * https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/uploadable.md
 *
 * This is to be kept compatible with the SonataMediaBundle MediaInterface to
 * allow integration with sonata.
 */
interface FileSystemInterface extends FileInterface
{
    /**
     * Get the path to the file on the file system.
     *
     * @return string
     */
    public function getFileSystemPath();
}