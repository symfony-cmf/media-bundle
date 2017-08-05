<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Editor;

/**
 * Interface used as helper to abstract the media browser needed for each
 * editor.
 */
interface BrowserEditorHelperInterface
{
    /**
     * Get the media browser url of the editor.
     *
     * @return string|false
     */
    public function getUrl();
}
