<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor\Helper;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\Request;

class CkeditorHelper extends DefaultHelper
{
    /**
     * {@inheritdoc}
     */
    public function getUploadResponse(Request $request, FileInterface $file)
    {
        $path    = $this->mediaManager->getFilePath($file);
        $url     = $this->router->generate('cmf_media_image_display', array('path' => ltrim($path, '/')));
        $funcNum = $request->query->get('CKEditorFuncNum');

        $data = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(".$funcNum.", '".$url."', 'success');</script>";

        $response = new Response($data);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}