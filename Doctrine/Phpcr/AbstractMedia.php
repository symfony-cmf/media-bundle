<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\HierarchyInterface as PhpcrHierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\Model\AbstractMedia as ModelAbstractMedia;

abstract class AbstractMedia extends ModelAbstractMedia implements HierarchyInterface, PhpcrHierarchyInterface
{
    /**
     * @var object
     */
    protected $parent;

    /**
     * @var string
     */
    protected $createdBy;

    /**
     * {@inheritdoc}
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        if ($parent instanceof Directory) {
            $parent->addChild($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * The createdBy is assigned by the content repository
     * This is the name of the (jcr) user that created the node
     *
     * @return string name of the (jcr) user who created the file
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
