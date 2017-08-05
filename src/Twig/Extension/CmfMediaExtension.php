<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Twig\Extension;

use Symfony\Cmf\Bundle\MediaBundle\Templating\Helper\CmfMediaHelper;

class CmfMediaExtension extends \Twig_Extension
{
    protected $mediaHelper;

    /**
     * @param CmfMediaHelper $mediaHelper
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
        return [
            new \Twig_SimpleFunction('cmf_media_download_url',
                [$this->mediaHelper, 'downloadUrl'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction('cmf_media_display_url',
                [$this->mediaHelper, 'displayUrl'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getName()
    {
        return 'cmf_media';
    }
}
