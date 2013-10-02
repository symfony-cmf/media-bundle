<?php

namespace Symfony\Cmf\Bundle\MediaBundle\File;

use Symfony\Cmf\Bundle\MediaBundle\Editor\UploadEditorHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface UploadFileHelperInterface
{
    /**
     * Allow non-uploaded files to validate for testing purposes.
     *
     * @param boolean $boolean
     */
    public function setAllowNonUploadedFiles($boolean);

    /**
     * Add an editor helper
     *
     * @param string                      $name
     * @param UploadEditorHelperInterface $helper
     */
    public function addEditorHelper($name, UploadEditorHelperInterface $helper);

    /**
     * Get helper
     *
     * @param $name leave null to get the default helper
     *
     * @return UploadEditorHelperInterface|null
     */
    public function getEditorHelper($name = null);

    /**
     * Handle the UploadedFile and create a FileInterface object specified by
     * the configured class.
     *
     * @param Request      $request
     * @param UploadedFile $uploadedFile
     *
     * @return FileInterface
     */
    public function handleUploadedFile(UploadedFile $uploadedFile);

    /**
     * Process upload and get a response
     *
     * @param Request        $request
     * @param UploadedFile[] $uploadedFiles optionally get the uploaded file(s)
     *      from the Request yourself
     *
     * @return Response
     */
    public function getUploadResponse(Request $request, array $uploadedFiles = array());
}