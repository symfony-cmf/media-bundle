<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\Util\PathHelper;
use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;

/**
 * A media manager suitable for doctrine phpcr-odm.
 *
 * @author Roel Sint
 */
class MediaManager implements MediaManagerInterface
{
    protected $managerRegistry;
    protected $managerName;
    protected $rootPath;

    /**
     * @param ManagerRegistry $registry
     * @param string          $managerName
     * @param string          $rootPath    path where the filesystem is located
     */
    public function __construct(ManagerRegistry $registry, $managerName, $rootPath = '/')
    {
        $this->managerRegistry = $registry;
        $this->managerName = $managerName;
        $this->rootPath = $rootPath;
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
     * @return DocumentManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(MediaInterface $media)
    {
        return $media->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlSafePath(MediaInterface $media)
    {
        return ltrim($media->getId(), '/');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaults(MediaInterface $media, $parentPath = null)
    {
        $class = ClassUtils::getClass($media);

        // check and add name if possible
        if (!$media->getName()) {
            if ($media->getId()) {
                $media->setName(PathHelper::getNodeName($media->getId()));
            } else {
                throw new \RuntimeException(sprintf(
                    'Unable to set defaults, Media of type "%s" does not have a name or id.',
                    $class
                ));
            }
        }

        $rootPath = is_null($parentPath) ? $this->rootPath : $parentPath;
        $path = ($rootPath === '/' ? $rootPath : $rootPath.'/').$media->getName();

        /** @var DocumentManager $dm */
        $dm = $this->getObjectManager();

        // TODO use PHPCR autoname
        if ($dm->find($class, $path)) {
            // path already exists
            $ext = pathinfo($media->getName(), PATHINFO_EXTENSION);
            $media->setName($media->getName().'_'.time().'_'.rand().($ext ? '.'.$ext : ''));
        }

        if (!$media->getParent()) {
            $parent = $dm->find(null, PathHelper::getParentPath($path));
            $media->setParent($parent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapPathToId($path, $rootPath = null)
    {
        // The path is being the id
        $id = PathHelper::absolutizePath($path, '/');

        if (is_string($rootPath) && 0 !== strpos($id, $rootPath)) {
            throw new \OutOfBoundsException(sprintf(
                'The path "%s" is out of the root path "%s" were the file system is located.',
                $path,
                $rootPath
            ));
        }

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function mapUrlSafePathToId($path, $rootPath = null)
    {
        return $this->mapPathToId($path, $rootPath);
    }
}
