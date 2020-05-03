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
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CmfMediaExtension extends AbstractExtension
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cmf_media_download_url',
                [$this->mediaHelper, 'downloadUrl'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction('cmf_media_display_url',
                [$this->mediaHelper, 'displayUrl'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getName(): string
    {
        return 'cmf_media';
    }
}
