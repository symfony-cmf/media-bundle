<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\BaseMedia;

class Directory extends BaseMedia implements DirectoryInterface
{
    /**
     * @var HierarchyInterface[] $children
     */
    protected $children;

    /**
     * Returns the contents of this directory.
     *
     * @return HierarchyInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}
