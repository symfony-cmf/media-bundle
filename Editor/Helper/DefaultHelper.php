<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor\Helper;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\MediaManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\Editor\EditorHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class DefaultHelper implements EditorHelperInterface
{
    protected $mediaManager;
    protected $router;

    /**
     * @param MediaManagerInterface $mediaManager
     * @param RouterInterface $router
     */
    public function __construct(MediaManagerInterface $mediaManager, RouterInterface $router)
    {
        $this->mediaManager = $mediaManager;
        $this->router       = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileDefaults(Request $request, FileInterface $file)
    {
        if (strlen($request->get('description'))) {
            $file->setDescription($request->get('description'));
        } elseif (strlen($request->get('caption'))) {
            $file->setDescription($request->get('caption'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadResponse(Request $request, FileInterface $file)
    {
        $path = $this->mediaManager->getFilePath($file);

        return new RedirectResponse($this->router->generate('cmf_media_image_display', array('path' => ltrim($path, '/'))));
    }
}