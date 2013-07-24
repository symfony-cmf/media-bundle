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
        if (is_null($name)) {
            $name = 'default';
        }

        return isset($this->helpers[$name]) ? $this->helpers[$name] : null;
    }
}