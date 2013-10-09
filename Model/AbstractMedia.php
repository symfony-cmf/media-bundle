<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Model;

use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;
use Symfony\Cmf\Bundle\MediaBundle\MetadataInterface;

abstract class AbstractMedia implements MediaInterface, MetadataInterface
{
    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var string $copyright
     */
    protected $copyright;

    /**
     * @var string $authorName
     */
    protected $authorName;

    /**
     * @var array $metadata
     */
    protected $metadata;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * String representation
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
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * {@inheritDoc}
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * {@inheritDoc}
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataValue($name, $default = null)
    {
        return isset($this->metadata[$name]) ? $this->metadata[$name] : $default;
    }

    /**
     * {@inheritDoc}
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
