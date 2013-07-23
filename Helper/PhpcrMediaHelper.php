<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Helper;

use PHPCR\Util\PathHelper;
use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;

class PhpcrMediaHelper implements MediaHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFilePath(MediaInterface $media)
    {
        return $media->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function mapPathToId($path, $rootPath = null)
    {
        // The path is being the id
        $id = PathHelper::absolutizePath($path, '/');

        if ($rootPath && 0 !== strpos($id, $rootPath)) {
            throw new \OutOfBoundsException(sprintf(
                'The path "%s" is out of the root path "%s" were the file system is located.',
                $path,
                $rootPath
            ));
        }

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentPath($path)
    {
        return PathHelper::getParentPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseName($path)
    {
        return PathHelper::getNodeName($path);
    }
}