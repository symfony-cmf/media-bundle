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
    protected $propertyMapping;

    /**
     * @param MediaManagerInterface $mediaManager
     * @param RouterInterface       $router
     * @param array                 $propertyMapping maps request parameters to
     * Media properties, fe. "caption" from the requests maps to "description"
     */
    public function __construct(MediaManagerInterface $mediaManager, RouterInterface $router, array $propertyMapping = array())
    {
        $this->mediaManager    = $mediaManager;
        $this->router          = $router;
        $this->propertyMapping = $propertyMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileDefaults(Request $request, FileInterface $file)
    {
        // map request parameters to Media properties
        foreach ($this->propertyMapping as $param => $property) {
            if (strlen($request->get($param))) {
                $setter = 'set' . ucfirst($property);
                $file->$setter($request->get($param));
            }
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