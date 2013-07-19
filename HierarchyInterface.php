<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for file and directory objects.
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
interface HierarchyInterface extends MediaInterface
{
    /**
     * Get the parent node.
     *
     * @return Object|null
     */
    public function getParent();

    /**
     * Set the parent node.
     *
     * @param Object $parent
     *
     * @return boolean
     */
    public function setParent($parent);
}
