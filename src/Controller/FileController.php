<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller to handle file downloads, uploads and other things that have a route.
 */
class FileController
{
    protected $managerRegistry;
    protected $managerName;
    protected $class;
    protected $rootPath;
    protected $mediaManager;
    protected $uploadFileHelper;
    protected $requiredUploadRole;
    /**
     * When moving to 2.0, rename this to $authorizationChecker.
     *
     * @var null|AuthorizationCheckerInterface
     */
    protected $securityContext;

    /**
     * @var null|TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param ManagerRegistry               $registry
     * @param string                        $managerName
     * @param string                        $class                fully qualified class
     *                                                            name of file
     * @param string                        $rootPath             path where the
     *                                                            filesystem is located
     * @param MediaManagerInterface         $mediaManager
     * @param UploadFileHelperInterface     $uploadFileHelper
     * @param string                        $requiredUploadRole   the role name for the security check
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     */
    public function __construct(
        ManagerRegistry $registry,
        $managerName,
        $class,
        $rootPath,
        MediaManagerInterface $mediaManager,
        UploadFileHelperInterface $uploadFileHelper,
        $requiredUploadRole,
        $authorizationChecker = null,
        $tokenStorage = null
    ) {
        $this->managerRegistry = $registry;
        $this->managerName = $managerName;
        $this->class = $class === '' ? null : $class;
        $this->rootPath = $rootPath;
        $this->mediaManager = $mediaManager;
        $this->uploadFileHelper = $uploadFileHelper;
        $this->requiredUploadRole = $requiredUploadRole;
        if ($authorizationChecker instanceof AuthorizationCheckerInterface && !$tokenStorage instanceof TokenStorageInterface) {
            throw new \InvalidArgumentException('Supply both authorization checker and token storage, or none of them');
        }
        $this->securityContext = $authorizationChecker;
        $this->tokenStorage = $authorizationChecker instanceof AuthorizationCheckerInterface ? $tokenStorage : $authorizationChecker;
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
     * managerName.
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }

    /**
     * Action to download a file object that has a route.
     *
     * @param string $path
     *
     * @return BinaryFileResponse|Response
     */
    public function downloadAction($path)
    {
        try {
            $id = $this->mediaManager->mapUrlSafePathToId($path, $this->rootPath);
        } catch (\OutOfBoundsException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $contentDocument = $this->getObjectManager()->find($this->class, $id);

        if (!$contentDocument || !$contentDocument instanceof FileInterface) {
            throw new NotFoundHttpException(sprintf(
                'Object with identifier %s cannot be resolved to a valid instance of Symfony\Cmf\Bundle\MediaBundle\FileInterface',
                $path
            ));
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

    /**
     * Action to upload a file.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $this->checkSecurityUpload($request);

        return $this->uploadFileHelper->getUploadResponse($request);
    }

    /**
     * Decide whether the user is allowed to upload a file.
     *
     * @param Request $request
     */
    protected function checkSecurityUpload(Request $request)
    {
        if (false === $this->requiredUploadRole) {
            return;
        }
        if ($this->securityContext
            && $this->tokenStorage->getToken()
            && $this->securityContext->isGranted($this->requiredUploadRole)
        ) {
            return;
        }

        throw new AccessDeniedException();
    }
}
