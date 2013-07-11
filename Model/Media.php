<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Model;

use Symfony\Cmf\Bundle\MediaBundle\MediaWriteInterface;

class Media implements MediaWriteInterface
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
     * @var \DateTime
     */
    protected $updatedAt;

    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
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
        return isset($metadata[$name]) ? $metadata[$name] : $default;
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
        $metadata = $this->getMetadata();
        unset($metadata[$name]);
        $this->setMetadata($metadata);
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}