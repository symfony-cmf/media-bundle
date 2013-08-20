<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Twig\Extension;

use Symfony\Cmf\Bundle\MediaBundle\Templating\Helper\CmfMediaHelper;

class CmfMediaExtension extends \Twig_Extension
{
    protected $mediaHelper;

    /**
     * @param MediaHelper $mediaHelper
     */
    public function __construct(CmfMediaHelper $mediaHelper)
    {
        $this->mediaHelper = $mediaHelper;
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
                array($this->mediaHelper, 'downloadUrl'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('cmf_media_display_url',
                array($this->mediaHelper, 'displayUrl'),
                array('is_safe' => array('html'))
            ),
        );
    }

    public function getName()
    {
        return 'cmf_media';
    }
}
