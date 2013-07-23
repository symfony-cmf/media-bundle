<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Templating\Helper;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\Helper\MediaHelperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

class MediaHelper extends Helper
{
    protected $mediaHelper;
    protected $generator;

    /**
     * Constructor.
     *
     * @param MediaHelperInterface  $mediaHelper
     * @param UrlGeneratorInterface $router      A Router instance
     */
    public function __construct(MediaHelperInterface $mediaHelper, UrlGeneratorInterface $router)
    {
        $this->mediaHelper = $mediaHelper;
        $this->generator   = $router;
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
        $path = $this->mediaHelper->getFilePath($file);

        return $this->generator->generate('cmf_media_download', array('path' => ltrim($path, '/')), $referenceType);
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
