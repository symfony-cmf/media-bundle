<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSytemInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        if ($contentDocument instanceof BinaryInterface && is_file($contentDocument->getContentAsStream())) {
            $file = $contentDocument->getContentAsStream();
        } elseif ($contentDocument instanceof FileSytemInterface) {
            $file = $contentDocument->getFileSystemPath();
        } else {
            $file = new \SplTempFileObject();
            $file->fwrite($contentDocument->getContentAsString());
            $file->rewind();
        }

        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', $contentDocument->getContentType());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $contentDocument->getName());

        return $response;
    }
}
