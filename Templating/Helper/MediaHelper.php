<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Templating\Helper;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\MediaManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

class MediaHelper extends Helper
{
    protected $mediaManager;
    protected $generator;

    /**
     * Constructor.
     *
     * @param MediaManagerInterface  $mediaManager
     * @param UrlGeneratorInterface  $router       A Router instance
     */
    public function __construct(MediaManagerInterface $mediaManager, UrlGeneratorInterface $router)
    {
        $this->mediaManager = $mediaManager;
        $this->generator    = $router;
    }

    /**
     * Generates a download URL from the given file.
     *
     * @param FileInterface  $file
     * @param Boolean|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    public function downloadUrl(FileInterface $file, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $path = $this->mediaManager->getFilePath($file);

        return $this->generator->generate('cmf_media_download', array('path' => ltrim($path, '/')), $referenceType);
    }

    /**
     * Generates a display URL from the given image.
     *
     * @param ImageInterface  $file
     * @param Boolean|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    public function displayUrl(ImageInterface $file, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $path = $this->mediaManager->getFilePath($file);

        return $this->generator->generate('cmf_media_image_display', array('path' => ltrim($path, '/')), $referenceType);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'cmf_media';
    }
}
