<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Gaufrette\Adapter;

use PHPCR\Util\PathHelper;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;

/**
 * Phpcr Cmf doctrine media adapter
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path.
 */
class PhpcrCmfMediaDoctrine extends AbstractCmfMediaDoctrine
{
    /**
     * {@inheritdoc}
     */
    protected function getFilePath(FileInterface $file)
    {
        return $file->getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function mapKeyToId($key)
    {
        return $this->computePath($key);
    }

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
