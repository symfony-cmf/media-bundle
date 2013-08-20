<?php

namespace Symfony\Cmf\Bundle\MediaBundle\File;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MediaBundle\Editor\UploadEditorHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UploadFileHelper
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
     * @param string                      $name
     * @param UploadEditorHelperInterface $helper
     */
    public function addEditorHelper($name, UploadEditorHelperInterface $helper)
    {
        $this->editorHelpers[$name] = $helper;
    }

    /**
     * Get helper
     *
     * @param $name leave null to get the default helper
     *
     * @return UploadEditorHelperInterface|null
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
     * Handle the UploadedFile and create a FileInterface object specified by
     * the configured class.
     *
     * @param Request      $request
     * @param UploadedFile $uploadedFile
     *
     * @return FileInterface
     */
    public function handleUploadedFile(UploadedFile $uploadedFile)
    {
        $this->validateFile($uploadedFile);

        /** @var $file FileInterface */
        $file = new $this->class();
        $file->setName($uploadedFile->getClientOriginalName());
        $file->copyContentFromFile($uploadedFile);

        try {
            $this->mediaManager->setDefaults($file, $this->rootPath);
        } catch (\RuntimeException $e) {
            throw new HttpException(409, $e->getMessage());
        }

        return $file;
    }

    /**
     * Process upload and get a response
     *
     * @param Request        $request
     * @param UploadedFile[] $uploadedFiles optionally get the uploaded file(s)
     *      from the Request yourself
     *
     * @return Response
     */
    public function getUploadResponse(Request $request, array $uploadedFiles = array())
    {
        /** @var \Symfony\Cmf\Bundle\MediaBundle\Editor\EditorHelperInterface $editorHelper */
        $editorHelper = $this->getEditorHelper($request->get('editor', 'default'));

        if (! $editorHelper) {
            throw new HttpException(409, sprintf(
                'Editor type "%s" not found, cannot process upload.',
                $request->get('editor', 'default')
            ));
        }

        if (count($uploadedFiles) === 0) {
            // by default get the first file
            $uploadedFiles = array($request->files->getIterator()->current());
        }

        // handle the uploaded file(s)
        $files = array();
        foreach ($uploadedFiles as $uploadedFile) {
            $file = $this->handleUploadedFile($uploadedFile);

            $editorHelper->setFileDefaults($request, $file);

            $this->getObjectManager()->persist($file);

            $files[] = $file;
        }

        // write created FileInterface file(s) to storage
        $this->getObjectManager()->flush();

        // response
        return $editorHelper->getUploadResponse($request, $files);
    }
}