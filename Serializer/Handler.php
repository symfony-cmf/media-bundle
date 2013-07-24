<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Serializer;

use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Cmf\Bundle\MediaBundle\Helper\MediaHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\Routing\RouterInterface;

class Handler
{
    protected $mediaHelper;
    protected $router;

    public function __construct(MediaHelperInterface $mediaHelper, RouterInterface $router)
    {
        $this->mediaHelper = $mediaHelper;
        $this->router      = $router;
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
        $path = $this->mediaHelper->getFilePath($image);
        $url = $this->router->generate('cmf_media_image_display', array('path' => ltrim($path, '/')), true);

        return array('id' => $image->getId(), 'url' => $url, 'alt' => $image->getDescription());
    }

}
