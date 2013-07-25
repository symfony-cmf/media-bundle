<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Twig\Extension;

use Symfony\Cmf\Bundle\MediaBundle\Templating\Helper\MediaHelper;

class MediaExtension extends \Twig_Extension
{
    protected $mediaManager;

    /**
     * @param MediaHelper $mediaManager
     */
    public function __construct(MediaHelper $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cmf_media_download_url',
                array($this->mediaManager, 'downloadUrl'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('cmf_media_display_url',
                array($this->mediaManager, 'displayUrl'),
                array('is_safe' => array('html'))
            ),
        );
    }

    public function getName()
    {
        return 'cmf_media';
    }
}
