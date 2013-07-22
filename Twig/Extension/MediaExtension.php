<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Twig\Extension;

use Symfony\Cmf\Bundle\MediaBundle\Templating\Helper\AbstractMediaHelper;

class MediaExtension extends \Twig_Extension
{
    protected $mediaHelper;

    public function __construct(AbstractMediaHelper $mediaHelper)
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
        );
    }

    public function getName()
    {
        return 'cmf_media';
    }
}
