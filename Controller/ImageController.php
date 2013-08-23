<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to handle basic image actions that have a route
 */
class ImageController extends FileController
{
    /**
     * Action to display an image object that has a route
     *
     * @param string $path
     */
    public function displayAction($path)
    {
        try {
            $id = $this->mediaManager->mapUrlSafePathToId($path);
        } catch (\OutOfBoundsException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $contentObject = $this->getObjectManager()->find($this->class, $id);

        if (! $contentObject || ! $contentObject instanceof ImageInterface) {
            throw new NotFoundHttpException(sprintf(
                'Object with identifier %s cannot be resolved to a valid instance of Symfony\Cmf\Bundle\MediaBundle\ImageInterface',
                $path
            ));
        }

        $response = new Response($contentObject->getContentAsString());
        $response->headers->set('Content-Type', $contentObject->getContentType());

        return $response;
    }
}
