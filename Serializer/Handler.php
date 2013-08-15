<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Serializer;

use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class Handler
{
    protected $mediaManager;
    protected $router;

    /**
     * @param MediaManagerInterface $mediaManager
     * @param RouterInterface $router
     */
    public function __construct(MediaManagerInterface $mediaManager, RouterInterface $router)
    {
        $this->mediaManager = $mediaManager;
        $this->router       = $router;
    }

    /**
     * Handles the serialization of an Image object
     *
     * @param JsonSerializationVisitor $visitor
     * @param ImageInterface $image
     * @return array
     */
    public function serializeImageToJson(JsonSerializationVisitor $visitor, ImageInterface $image)
    {
        $urlSafePath = $this->mediaManager->getUrlSafePath($image);
        $url = $this->router->generate('cmf_media_image_display', array('path' => $urlSafePath), true);

        return array('id' => $image->getId(), 'url' => $url, 'alt' => $image->getDescription());
    }

}
