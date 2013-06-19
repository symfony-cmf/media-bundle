<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;

/**
 * Controller to handle file downloads for things that have a route
 */
class DownloadController
{
    /**
     * Action to download a document that has a route
     *
     * @param FileInterface $contentDocument
     */
    public function downloadAction($contentDocument)
    {
        if (! $contentDocument instanceof FileInterface) {
            throw new NotFoundHttpException('Content is no file');
        }

        // TODO: can we use the BinaryFileResponse here? or adapt it to use it?
        header('Content-Type: ' . $contentDocument->getContentType());
        fpassthru($contentDocument->getBinaryContent());
    }
}
