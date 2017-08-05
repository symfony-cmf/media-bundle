<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

/**
 * Note: the modified information is stored in the document because it has no
 * content child (Resource) doing it.
 */
class Media extends AbstractMedia
{
    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $updatedBy;

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Getter for updatedBy
     * The createdBy is assigned by the content repository
     * This is the name of the (jcr) user that updated the node.
     *
     * @return string name of the (jcr) user who updated the file
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
