<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Templating\Helper;

use Liip\ImagineBundle\Templating\Helper\ImagineHelper;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\MediaManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

class MediaHelper extends Helper
{
    protected $mediaManager;
    protected $generator;
    protected $useImagine;
    protected $imagineHelper;

    /**
     * Constructor.
     *
     * @param MediaManagerInterface  $mediaManager
     * @param UrlGeneratorInterface  $router       A Router instance
     */
    public function __construct(MediaManagerInterface $mediaManager, UrlGeneratorInterface $router, $useImagine = false, ImagineHelper $imagineHelper = null)
    {
        $this->mediaManager  = $mediaManager;
        $this->generator     = $router;
        $this->useImagine    = $useImagine;
        $this->imagineHelper = $imagineHelper;
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
     * @param array           $options
     * @param Boolean|string  $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    public function displayUrl(ImageInterface $file, array $options = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $urlSafePath = ltrim($this->mediaManager->getFilePath($file), '/');

        if ($this->useImagine && $this->imagineHelper && isset($options['imagine_filter']) && is_string($options['imagine_filter'])) {
            return $this->imagineHelper->filter(
                $urlSafePath,
                $options['imagine_filter'],
                $referenceType === UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->generator->generate('cmf_media_image_display', array('path' => $urlSafePath), $referenceType);
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
