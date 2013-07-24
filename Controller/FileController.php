<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\Editor\EditorManagerInterface;
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
    protected $editorManager;

    /**
     * @param ManagerRegistry $registry
     * @param string          $managerName
     * @param string          $class       fully qualified class name of file
     * @param string          $rootPath    path where the filesystem is located
     * @param MediaHelperInterface $mediaHelper
     * @param EditorManagerInterface $editorManager
     */
    public function __construct(
        ManagerRegistry $registry,
        $managerName,
        $class,
        $rootPath = '/',
        MediaHelperInterface $mediaHelper,
        EditorManagerInterface $editorManager)
    {
        $this->managerRegistry = $registry;
        $this->managerName     = $managerName;
        $this->class           = $class === '' ? null : $class;
        $this->rootPath        = $rootPath;
        $this->mediaHelper     = $mediaHelper;
        $this->editorManager   = $editorManager;
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
        /** @var \Symfony\Cmf\Bundle\MediaBundle\Editor\EditorHelperInterface $editorHelper */
        $editorHelper = $this->editorManager->getHelper($request->get('editor', 'default'));

        if (! $editorHelper) {
            throw new HttpException(409, sprintf(
                'Editor type "%s" not found, cannot process upload.',
                $request->get('editor', 'default')
            ));
        }

        $files = $request->files;

        /** @var $file UploadedFile */
        $uploadedFile = $files->getIterator()->current();
        $this->validateFile($uploadedFile);

        /** @var $image FileInterface */
        $file = new $this->class;
        $file->setName($uploadedFile->getClientOriginalName());
        $file->copyContentFromFile($uploadedFile);

        $editorHelper->setFileDefaults($request, $file);

        try {
            $this->mediaHelper->createFilePath($file, $this->rootPath);
        } catch (\RuntimeException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        // persist
        $this->getObjectManager()->persist($file);
        $this->getObjectManager()->flush();

        // response
        return $editorHelper->getUploadResponse($request, $file);
    }
}
