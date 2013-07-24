<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor;

/**
 * Manage editor helper classes
 */
interface EditorManagerInterface
{
    /**
     * Add an editor helper
     *
     * @param string                $name
     * @param EditorHelperInterface $helper
     */
    public function addHelper($name, EditorHelperInterface $helper);

    /**
     * Get helper
     *
     * @param $name leave null to get the default helper
     *
     * @return EditorHelperInterface|null
     */
    public function getHelper($name = null);
}