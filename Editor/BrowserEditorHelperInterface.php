<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor;

/**
 * Interface used as helper to abstract the media browser needed for each
 * editor.
 */
interface BrowserEditorHelperInterface
{
    /**
     * Get the media browser url of the editor
     *
     * @return string|false
     */
    public function getUrl();
}