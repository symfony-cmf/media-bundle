<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor\Helper;

use Symfony\Cmf\Bundle\MediaBundle\Editor\UploadEditorHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class UploadDefaultHelper implements UploadEditorHelperInterface
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
    public function getUploadResponse(Request $request, array $files)
    {
        if (!isset($files[0]) && !$files[0] instanceof FileInterface) {
            throw new \InvalidArgumentException(
                'Provide at least one Symfony\Cmf\Bundle\MediaBundle\FileInterface file.'
            );
        }

        $urlSafePath = $this->mediaManager->getUrlSafePath($files[0]);

        if ($files[0] instanceof ImageInterface) {
            return new RedirectResponse($this->router->generate('cmf_media_image_display', array('path' => $urlSafePath)));
        } else {
            return new RedirectResponse($request->headers->get('referer'));
        }
    }
}