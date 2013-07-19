<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\Model\Media as BaseMedia;

class Media extends BaseMedia
{
    /**
     * @var string
     */
    protected $createdBy;

    /**
     * @var string
     */
    protected $updatedBy;

    /**
     * Getter for createdBy
     * The createdBy is assigned by the content repository
     * This is the name of the (jcr) user that created the node
     *
     * @return string name of the (jcr) user who created the file
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Getter for updatedBy
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
