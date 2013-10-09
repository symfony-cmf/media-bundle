<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Document\Folder;
use Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;

class Directory extends Folder implements DirectoryInterface
{
    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $updatedBy;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->nodename;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->nodename = $name;
    }

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
    public function getCreatedAt()
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * The createdBy is assigned by the content repository
     * This is the name of the (jcr) user that updated the node
     *
     * @return string name of the (jcr) user who updated the file
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
