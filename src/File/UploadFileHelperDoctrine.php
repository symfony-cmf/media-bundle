<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class UploadFileHelperDoctrine implements UploadFileHelperInterface
{
    protected $managerRegistry;
    protected $managerName;
    protected $class;
    protected $rootPath;
    protected $mediaManager;
    protected $editorHelpers;
    protected $allowNonUploadedFiles = false;

    /**
     * @param ManagerRegistry       $registry
     * @param string                $managerName
     * @param string                $class        fully qualified class name of file
     * @param string                $rootPath     path where the filesystem is located
     * @param MediaManagerInterface $mediaManager
     */
    public function __construct(
        ManagerRegistry $registry,
        $managerName,
        $class,
        $rootPath,
        MediaManagerInterface $mediaManager)
    {
        $this->managerRegistry = $registry;
        $this->managerName = $managerName;
        $this->setClass($class);
        $this->rootPath = $rootPath;
        $this->mediaManager = $mediaManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowNonUploadedFiles($boolean)
    {
        $this->allowNonUploadedFiles = $boolean;
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
        if (empty($class)) {
            $this->class = null;

            return;
        }

        if (!is_subclass_of($class, 'Symfony\Cmf\Bundle\MediaBundle\FileInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not implement Symfony\Cmf\Bundle\MediaBundle\FileInterface',
                $class
            ));
        }
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
     * managerName.
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    public function addEditorHelper($name, UploadEditorHelperInterface $helper)
    {
        $this->editorHelpers[$name] = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditorHelper($name = null)
    {
        if ($name && isset($this->editorHelpers[$name])) {
            return $this->editorHelpers[$name];
        }

        return isset($this->editorHelpers['default']) ? $this->editorHelpers['default'] : null;
    }

    /**
     * Validate the uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return bool true either returns true or throws an exception
     *
     * @throws UploadException if the upload failed for some reason
     */
    protected function validateFile(UploadedFile $file)
    {
        if ($file->isValid()) {
            return true;
        }

        throw new UploadException($this->getErrorMessage($file));
    }

    /**
     * Returns an informative upload error message.
     *
     * Copied from UploadedFile because its only public since 2.4
     *
     * @param UploadedFile $file the file with the error
     *
     * @return string The error message regarding the specified error code
     */
    private function getErrorMessage(UploadedFile $file)
    {
        $errorCode = $file->getError();
        static $errors = [
            UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize ini directive (limit is %d kb).',
            UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
            UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk.',
            UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
            UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension.',
        ];

        $maxFilesize = $errorCode === UPLOAD_ERR_INI_SIZE ? $file->getMaxFilesize() / 1024 : 0;
        $message = isset($errors[$errorCode]) ? $errors[$errorCode] : 'The file "%s" was not uploaded due to an unknown error.';

        return sprintf($message, $file->getClientOriginalName(), $maxFilesize);
    }

    /**
     * {@inheritdoc}
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, $class = null)
    {
        $this->validateFile($uploadedFile);

        $class = $class ?: $this->class;
        /** @var $file FileInterface */
        $file = new $class();
        if (!$file instanceof FileInterface) {
            throw new UploadException(sprintf('Invalid class %s specified', $class));
        }
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
     * {@inheritdoc}
     */
    public function getUploadResponse(Request $request, array $uploadedFiles = [])
    {
        $editorHelper = $this->getEditorHelper($request->get('editor', 'default'));

        if (!$editorHelper) {
            throw new HttpException(409, sprintf(
                'Editor type "%s" not found, cannot process upload.',
                $request->get('editor', 'default')
            ));
        }

        if (count($uploadedFiles) === 0) {
            // by default get the first file
            $uploadedFiles = [$request->files->getIterator()->current()];
        }

        // handle the uploaded file(s)
        $files = [];
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
