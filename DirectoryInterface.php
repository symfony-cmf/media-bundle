<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for file objects containing directories.
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path.
 * For ORM the file path can concatenate the directory identifiers with '/'
 * and ends with the file identifier. For a nice path a slug could be used
 * as identifier.
 *
 * For ORM you could use:
 * - https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md
 * - https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/sluggable.md
 *
 * This is to be kept compatible with the Gaufrette adapter to be able to use a
 * filesystem with directories.
 */
interface DirectoryInterface extends FileInterface
{
    /**
     * Get the parent directory.
     *
     * @return DirectoryInterface|null
     */
    public function getParentDirectory();

    /**
     * Set the parent directory.
     *
     * @param DirectoryInterface $parent
     *
     * @return boolean
     */
    public function setParentDirectory(DirectoryInterface $parent);

    /**
     * Get full file path: /path/to/file/filename.ext
     *
     * @return string
     */
    public function getPath();
}