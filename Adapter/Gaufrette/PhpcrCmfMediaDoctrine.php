<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Gaufrette\Adapter;

use PHPCR\Util\PathHelper;

class PhpcrCmfMediaDoctrine extends AbstractCmfMediaDoctrine
{
    /**
     * {@inheritDoc}
     */
    protected function getParentPath($path)
    {
        return PathHelper::getParentPath($path);
    }

    /**
     * {@inheritDoc}
     */
    protected function getBaseName($path)
    {
        return PathHelper::getNodeName($path);
    }
}