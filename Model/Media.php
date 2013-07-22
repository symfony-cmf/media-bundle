<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Model;

use Symfony\Cmf\Bundle\MediaBundle\MetadataInterface;

class Media extends BaseMedia implements MetadataInterface
{
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
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param string $name
     * @param null $default
     * @return null
     */
    public function getMetadataValue($name, $default = null)
    {
        return isset($metadata[$name]) ? $metadata[$name] : $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
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
}
