<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use PHPCR\Util\PathHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to handle file downloads for things that have a route
 */
class PhpcrDownloadController extends AbstractDownloadController
{
    /**
     * {@inheritdoc}
     */
    protected function mapPathToId($path)
    {
        // The path is being the id
        $id = PathHelper::absolutizePath($path, '/');

        if (0 !== strpos($id, $this->rootPath)) {
            throw new NotFoundHttpException(sprintf(
                'The path "%s" is out of the root path "%s" were the file system is located.',
                $path,
                $this->rootPath
            ));
        }

        return $id;
    }
}
