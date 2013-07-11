<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Gaufrette\Adapter;

use PHPCR\Util\PathHelper;

/**
 * Phpcr Cmf doctrine media adapter
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path, set "fullPathId" to true.
 */
class PhpcrCmfMediaDoctrine extends AbstractCmfMediaDoctrine
{
    /**
     * {@inheritdoc}
     */
    protected function getParentPath($path)
    {
        return PathHelper::getParentPath($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseName($path)
    {
        return PathHelper::getNodeName($path);
    }
}
