<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Templating\Helper;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

abstract class AbstractMediaHelper extends Helper
{
    protected $generator;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $router A Router instance
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->generator = $router;
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
        $path = $this->getFilePath($file);

        return $this->generator->generate('cmf_media_download', array('path' => ltrim($path, '/')), $referenceType);
    }

    /**
     * Get full file path: /path/to/file/filename.ext
     *
     * @return string
     */
    abstract protected function getFilePath(FileInterface $file);

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
