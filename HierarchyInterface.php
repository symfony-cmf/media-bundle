<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for file and directory objects.
 *
 * For PHPCR the id is being the path.
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
