<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\BaseMedia;

class Directory extends BaseMedia implements DirectoryInterface
{
    /**
     * @var Object $parent
     */
    protected $parent;

    /**
     * @var HierarchyInterface[] $children
     */
    protected $children;

    /**
     * @var int $size
     */
    protected $size;

    /**
     * Get the parent node.
     *
     * @return Object|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent node.
     *
     * @param Object $parent
     *
     * @return boolean
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns the contents of this directory.
     *
     * @return HierarchyInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get full path: /path/of/directory | /path/to/file/filename.ext
     *
     * @return string
     */
    public function getPath()
    {
        return (string) $this->id;
    }

    /**
     * Get the file size in bytes
     *
     * @return integer
     */
    public function getSize()
    {
        return (int) $this->size;
    }

}
