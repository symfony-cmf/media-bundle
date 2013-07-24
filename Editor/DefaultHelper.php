<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\Helper\MediaHelperInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class DefaultHelper implements EditorHelperInterface
{
    protected $mediaHelper;
    protected $router;

    /**
     * @param MediaHelperInterface $mediaHelper
     * @param RouterInterface $router
     */
    public function __construct(MediaHelperInterface $mediaHelper, RouterInterface $router)
    {
        $this->mediaHelper = $mediaHelper;
        $this->router      = $router;
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
        $path = $this->mediaHelper->getFilePath($file);

        return new RedirectResponse($this->router->generate('cmf_media_image_display', array('path' => ltrim($path, '/'))));
    }
}