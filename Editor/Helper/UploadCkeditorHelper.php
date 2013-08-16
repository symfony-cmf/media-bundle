<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor\Helper;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadCkeditorHelper extends UploadDefaultHelper
{
    /**
     * {@inheritdoc}
     */
    public function getUploadResponse(Request $request, FileInterface $file)
    {
        $urlSafePath = $this->mediaManager->getUrlSafePath($file);
        $url         = $this->router->generate('cmf_media_image_display', array('path' => $urlSafePath));
        $funcNum     = $request->query->get('CKEditorFuncNum');

        $data = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(".$funcNum.", '".$url."', 'success');</script>";

        $response = new Response($data);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}