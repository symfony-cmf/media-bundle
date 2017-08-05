<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Model;

use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;
use Symfony\Cmf\Bundle\MediaBundle\MetadataInterface;

abstract class AbstractMedia implements MediaInterface, MetadataInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $copyright;

    /**
     * @var string
     */
    protected $authorName;

    /**
     * @var array
     */
    protected $metadata;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * String representation.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataValue($name, $default = null)
    {
        return isset($this->metadata[$name]) ? $this->metadata[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataValue($name, $value)
    {
        $this->metadata[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetMetadataValue($name)
    {
        unset($this->metadata[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getUpdatedAt();
}
