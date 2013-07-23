<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;
use Symfony\Cmf\Bundle\MediaBundle\Helper\MediaHelperInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to handle file downloads, uploads and other things that have a route
 */
class FileController
{
    protected $managerRegistry;
    protected $managerName;
    protected $class;
    protected $rootPath;
    protected $mediaHelper;

    /**
     * @param ManagerRegistry $registry
     * @param string          $managerName
     * @param string          $class       fully qualified class name of file
     * @param string          $rootPath    path where the filesystem is located
     * @param MediaHelperInterface $mediaHelper
     */
    public function __construct(ManagerRegistry $registry, $managerName, $class, $rootPath = '/', MediaHelperInterface $mediaHelper)
    {
        $this->managerRegistry = $registry;
        $this->managerName     = $managerName;
        $this->class           = $class === '' ? null : $class;
        $this->rootPath        = $rootPath;
        $this->mediaHelper     = $mediaHelper;
    }

    /**
     * Set the managerName to use to get the object manager;
     * if not called, the default manager will be used.
     *
     * @param string $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    /**
     * Set the class to use to get the file object;
     * if not called, the default class will be used.
     *
     * @param string $class fully qualified class name of file
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Set the root path were the file system is located;
     * if not called, the default root path will be used.
     *
     * @param string $rootPath
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Get the object manager from the registry, based on the current
     * managerName
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }

    /**
     * Validate the uploaded file
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    protected function validateFile(UploadedFile $file)
    {
        return true;
    }

    /**
     * Get description if set in the request for an upload
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getDescription(Request $request)
    {
        if (strlen($request->get('description'))) {
            return $request->get('description');
        } elseif (strlen($request->get('caption'))) {
            return $request->get('caption');
        }

        return null;
    }

    /**
     * TODO: change to use an upload response factory;
     * what should be the default response for an uploaded file?
     *
     * Generate the response for an uploaded image
     *
     * @param FileInterface $image
     * @param UploadedFile $uploadedFile
     *
     * @return Response
     */
    protected function generateUploadResponse(FileInterface $file, UploadedFile $uploadedFile)
    {
        $path = $this->mediaHelper->getFilePath($file);

        return new RedirectResponse($this->router->generate('cmf_media_image_display', array('path' => ltrim($path, '/'))));
    }

    /**
     * Action to download a file object that has a route
     *
     * @param string $id
     */
    public function downloadAction($path)
    {
        try {
            $id = $this->mediaHelper->mapPathToId($path, $this->rootPath);
        } catch (\OutOfBoundsException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $contentDocument = $this->getObjectManager()->find($this->class, $id);

        if (! $contentDocument || ! $contentDocument instanceof FileInterface) {
            throw new NotFoundHttpException('Content is no file');
        }

        $file = false;

        if ($contentDocument instanceof BinaryInterface) {
            $metadata = stream_get_meta_data($contentDocument->getContentAsStream());

            $file = isset($metadata['uri']) ? $metadata['uri'] : false;
        } elseif ($contentDocument instanceof FileSystemInterface) {
            $file = $contentDocument->getFileSystemPath();
        }

        if ($file) {
            $response = new BinaryFileResponse($file);
            $response->headers->set('Content-Type', $contentDocument->getContentType());
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $contentDocument->getName());
        } else {
            $response = new Response($contentDocument->getContentAsString());
            $response->headers->set('Content-Type', $contentDocument->getContentType());
            $response->headers->set('Content-Length', $contentDocument->getSize());
            $response->headers->set('Content-Transfer-Encoding', 'binary');

            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $contentDocument->getName());
            $response->headers->set('Content-Disposition', $disposition);
        }

        return $response;
    }

    public function uploadAction(Request $request)
    {
        $files = $request->files;

        /** @var $file UploadedFile */
        $uploadedFile = $files->getIterator()->current();
        $this->validateFile($uploadedFile);

        $name = $uploadedFile->getClientOriginalName();
        $description = $this->getDescription($request);

        /** @var $image FileInterface */
        $file = new $this->class;
        $file->setName($name);
        if ($description) {
            $file->setDescription($name);
        }
        $file->copyContentFromFile($uploadedFile);

        try {
            $this->mediaHelper->createFilePath($file, $this->rootPath);
        } catch (\RuntimeException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        // persist
        $this->getObjectManager()->persist($file);
        $this->getObjectManager()->flush();

        // file upload via CKEditor
        if ($request->query->get('CKEditor')) {
            $response = $this->generateUploadResponse($file, $uploadedFile);
            $data = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(" . $request->query->get('CKEditorFuncNum') . ", '" . $response->getTargetUrl() . "', 'success');</script>";

            $response = new Response($data);
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        } else {
            return $this->generateUploadResponse($file, $uploadedFile);
        }
    }
}
