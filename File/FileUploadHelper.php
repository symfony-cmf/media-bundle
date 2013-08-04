<?php

namespace Symfony\Cmf\Bundle\MediaBundle\File;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\MediaManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\Editor\EditorHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileUploadHelper
{
    protected $managerRegistry;
    protected $managerName;
    protected $class;
    protected $rootPath;
    protected $mediaManager;
    protected $editorHelpers;

    /**
     * @param ManagerRegistry        $registry
     * @param string                 $managerName
     * @param string                 $class         fully qualified class name of file
     * @param string                 $rootPath      path where the filesystem is located
     * @param MediaManagerInterface  $mediaManager
     * @param EditorManager          $editorManager
     */
    public function __construct(
        ManagerRegistry $registry,
        $managerName,
        $class,
        $rootPath = '/',
        MediaManagerInterface $mediaManager)
    {
        $this->managerRegistry = $registry;
        $this->managerName     = $managerName;
        $this->class           = $class === '' ? null : $class;
        $this->rootPath        = $rootPath;
        $this->mediaManager    = $mediaManager;
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
     * Add an editor helper
     *
     * @param string                $name
     * @param EditorHelperInterface $helper
     */
    public function addEditorHelper($name, EditorHelperInterface $helper)
    {
        $this->editorHelpers[$name] = $helper;
    }

    /**
     * Get helper
     *
     * @param $name leave null to get the default helper
     *
     * @return EditorHelperInterface|null
     */
    public function getEditorHelper($name = null)
    {
        if ($name && isset($this->editorHelpers[$name])) {
            return $this->editorHelpers[$name];
        }

        return isset($this->editorHelpers['default']) ? $this->editorHelpers['default'] : null;
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
     * Process upload and get a response
     *
     * @return Response
     */
    public function getUploadResponse(Request $request)
    {
        /** @var \Symfony\Cmf\Bundle\MediaBundle\Editor\EditorHelperInterface $editorHelper */
        $editorHelper = $this->getEditorHelper($request->get('editor', 'default'));

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
            $this->mediaManager->createFilePath($file, $this->rootPath);
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