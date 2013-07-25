<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor;

class EditorManager implements EditorManagerInterface
{
    /**
     * @var EditorHelperInterface[]
     */
    protected $helpers;

    /**
     * {@inheritdoc}
     */
    public function addHelper($name, EditorHelperInterface $helper)
    {
        $this->helpers[$name] = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getHelper($name = null)
    {
        if ($name && isset($this->helpers[$name])) {
            return $this->helpers[$name];
        }

        return isset($this->helpers['default']) ? $this->helpers['default'] : null;
    }
}