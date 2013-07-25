<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use PHPCR\Util\PathHelper;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\MediaManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MediaManager implements MediaManagerInterface
{
    protected $managerRegistry;
    protected $managerName;

    /**
     * @param ManagerRegistry $registry
     * @param string          $managerName
     */
    public function __construct(ManagerRegistry $registry, $managerName)
    {
        $this->managerRegistry = $registry;
        $this->managerName     = $managerName;
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
     * {@inheritdoc}
     */
    public function getFilePath(MediaInterface $media)
    {
        return $media->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function createFilePath(MediaInterface $media, $rootPath = null)
    {
        $path = ($rootPath === '/' ? $rootPath : $rootPath . '/') . $media->getName();

        /** @var \Doctrine\ODM\PHPCR\DocumentManager $dm */
        $dm = $this->getObjectManager();

        // TODO use PHPCR autoname
        $class = ClassUtils::getClass($media);
        if ($dm->find($class, $path)) {
            // path already exists
            $media->setName($media->getName() . '_' . time() . '_' . rand());
        }

        $parent = $dm->find(null, PathHelper::getParentPath($path));
        $media->setParent($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function mapPathToId($path, $rootPath = null)
    {
        // The path is being the id
        $id = PathHelper::absolutizePath($path, '/');

        if ($rootPath && 0 !== strpos($id, $rootPath)) {
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
    public function getParentPath($path)
    {
        return PathHelper::getParentPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseName($path)
    {
        return PathHelper::getNodeName($path);
    }
}