<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Editor;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface used as helper to handle file uploads and the specific response
 * needed for each editor, may this be json, javascript or something else.
 */
interface UploadEditorHelperInterface
{
    /**
     * Set file defaults from request
     *
     * @param Request $request
     * @param FileInterface $file
     */
    public function setFileDefaults(Request $request, FileInterface $file);

    /**
     * Get a response for the upload action of the editor
     *
     * @param Request $request
     * @param FileInterface[] $files
     *
     * @return Response
     *
     * @throws InvalidArgumentException if no FileInterface file is provided
     */
    public function getUploadResponse(Request $request, array $files);
}