<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Adapter\Gaufrette;

use Doctrine\Common\Persistence\ManagerRegistry;
use Gaufrette\Adapter;
use Gaufrette\Adapter\ChecksumCalculator;
use Gaufrette\Adapter\ListKeysAware;
use Gaufrette\Adapter\MetadataSupporter;
use Gaufrette\Util;
use Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\MetadataInterface;

/**
 * Cmf doctrine media adapter.
 *
 * Gaufrette uses a key to identify a file or directory. This adapter uses a
 * filesystem path, like /path/to/file/filename.ext, as key.
 *
 * The method getFilePath is used to get the path for a file or directory
 * object. The method mapKeyToId maps a path back to an id.
 *
 * If you set the autoFlush flag to false, you will get better performance but
 * must ensure that flush is called after all media operations are done.
 */
abstract class AbstractCmfMediaDoctrine implements Adapter, ChecksumCalculator, ListKeysAware, MetadataSupporter
{
    protected $managerRegistry;
    protected $managerName;
    protected $class;
    protected $mediaManager;
    protected $rootPath;
    protected $create;
    protected $dirClass;
    protected $identifier;
    protected $autoFlush;

    protected $keys;

    /**
     * Constructor.
     *
     * @param ManagerRegistry       $registry
     * @param string                $managerName
     * @param string                $class        fully qualified class name of file
     * @param MediaManagerInterface $mediaManager
     * @param string                $rootPath     path where the filesystem is located
     * @param bool                  $create       whether to create the directory if
     *                                            it does not exist (default FALSE)
     * @param string                $dirClass     fully qualified class name for dirs
     *                                            (default NULL: dir is same as file)
     * @param string                $identifier   property used to identify a file and
     *                                            lookup (default NULL: let Doctrine
     *                                            determine the identifier)
     * @param bool                  $autoFlush    whether to immediately flush write
     *                                            and delete actions (default: true)
     */
    public function __construct(
        ManagerRegistry $registry,
        $managerName,
        $class,
        MediaManagerInterface $mediaManager,
        $rootPath = '/',
        $create = false,
        $dirClass = null,
        $identifier = null,
        $autoFlush = true)
    {
        $this->managerRegistry = $registry;
        $this->managerName = $managerName;
        $this->class = $class;
        $this->mediaManager = $mediaManager;
        $this->rootPath = Util\Path::normalize($rootPath);
        $this->create = $create;
        $this->dirClass = $dirClass;
        $this->identifier = $identifier;
        $this->autoFlush = $autoFlush;

        if (!is_subclass_of($class, 'Symfony\Cmf\Bundle\MediaBundle\FileInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not implement Symfony\Cmf\Bundle\MediaBundle\FileInterface',
                $class
            ));
        }

        if ($identifier && !$this->getObjectManager()->getClassMetadata($class)->hasField($identifier)) {
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not have the field "%s" to be used as identifier',
                $class,
                $identifier
            ));
        }

        if ($dirClass) {
            if (!is_subclass_of($dirClass, 'Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface')) {
                throw new \InvalidArgumentException(sprintf(
                    'The class "%s" does not implement Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface',
                    $dirClass
                ));
            }

            if ($identifier && !$this->getObjectManager()->getClassMetadata($dirClass)->hasField($identifier)) {
                throw new \InvalidArgumentException(sprintf(
                    'The class "%s" does not have the field "%s" to be used as identifier',
                    $dirClass,
                    $identifier
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        $file = $this->find($key);

        return $file ? $file->getContentAsString() : '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $content)
    {
        if ($this->exists($key)) {
            $file = $this->find($key);
            if (!$file instanceof FileInterface) {
                return false;
            }
        } else {
            $filePath = $this->computePath($key);

            $this->ensureDirectoryExists($this->getParentPath($filePath), $this->create);

            $file = new $this->class();
            $parent = $this->find($this->getParentPath($key));

            $this->setFileDefaults($filePath, $file, $parent);
        }

        $file->setContentFromString($content);

        $this->getObjectManager()->persist($file);
        if ($this->autoflush) {
            $this->getObjectManager()->flush();
        }

        return $file->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return (bool) $this->find($key) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        if (is_null($this->keys)) {
            $keys = [];

            $files = $this->findAll();
            foreach ($files as $file) {
                $keys[] = $this->computeKey($this->getFilePath($file));
            }

            $this->keys = sort($keys);
        }

        return $this->keys;
    }

    /**
     * {@inheritdoc}
     */
    public function mtime($key)
    {
        $file = $this->find($key);

        return $file && $file->getUpdatedAt() ? $file->getUpdatedAt()->getTimestamp() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $file = $this->find($key);

        if ($file) {
            $this->getObjectManager()->remove($file);
            if ($this->autoflush) {
                $this->getObjectManager()->flush();
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($sourceKey, $targetKey)
    {
        // not supported, extend for a specific implementation
        //
        // a key is always a path ending with the filename
        // a rename is:
        // (1) a move to another parent directory
        // (2) and/or a filename change
        //
        // (1) can always be supported for files implementing the
        //     DirectoryInterface
        // (2) renaming the filename part is specific:
        //     - ORM: do not support renaming the filename (=identifier) if it
        //       is an auto generated id
        //     - ORM: can support renaming the filename (=identifier) if it is
        //       a slug that can be changed
        //     - PHPCR: can support renaming the filename if the nodename can
        //       be changed
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectory($key)
    {
        if ('/' === $key) {
            return true;
        }

        $file = $this->find($key, true);

        if ($file instanceof $this->class) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function checksum($key)
    {
        $file = $this->find($key);

        return Util\Checksum::fromContent(($file ? $file->getContentAsString() : ''));
    }

    /**
     * {@inheritdoc}
     */
    public function listKeys($prefix = '')
    {
        $dirKeys = $fileKeys = [];
        $files = $this->findAll($prefix);

        foreach ($files as $file) {
            $key = $this->computeKey($this->getFilePath($file));

            if ($file instanceof FileInterface) {
                $fileKeys = $key;
            } else {
                $dirKeys[] = $key;
            }
        }

        return [
            'dirs' => sort($dirKeys),
            'keys' => sort($fileKeys),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If file cannot be found or cannot be written
     *                           to
     */
    public function setMetadata($key, $metadata)
    {
        $file = $this->find($key);

        if (!$file) {
            throw new \RuntimeException(sprintf('The file "%s" does not exist.', $key));
        }

        if (!$file instanceof MetadataInterface) {
            $type = is_object($file) ? get_class($file) : gettype($file);
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not implement Symfony\Cmf\Bundle\MediaBundle\MetadataInterface',
                $type
            ));
        }

        $file->setMetadata($metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key)
    {
        $file = $this->find($key);

        return $file ? $file->getMetadata() : [];
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
     * Get the object manager from the registry, based on the current
     * managerName.
     *
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }

    /**
     * Whether to flush Doctrine directly after a persist,
     * disable for batch actions.
     *
     * @param $bool boolean
     */
    public function setAutoFlush($bool)
    {
        $this->autoFlush = $bool;
    }

    /**
     * Find a file object for the given key.
     *
     * @param string|int $key identifier
     * @param bool       $dir directly try to find a directory
     *
     * @return FileInterface
     */
    protected function find($key, $dir = false)
    {
        if (!isset($key)) {
            return;
        }

        $id = $this->mapKeyToId($key);
        $file = null;

        // find file
        if (!$dir || ($dir && !$this->dirClass)) {
            if ($this->identifier) {
                $file = $this->getObjectManager()
                    ->getRepository($this->class)
                    ->findOneBy([$this->identifier => $id])
                ;
            } else {
                $file = $this->getObjectManager()->getRepository($this->class)->find($id);
            }
        }

        // find directory from the configured directory repository
        if (!$file && $this->dirClass) {
            if ($this->identifier) {
                $file = $this->getObjectManager()
                    ->getRepository($this->class)
                    ->findOneBy([$this->identifier => $id])
                ;
            } else {
                $file = $this->getObjectManager()->getRepository($this->dirClass)->find($id);
            }
        }

        return $file;
    }

    /**
     * Get all files and directories,
     * extend for a specific and more efficient implementation.
     *
     * @param string $prefix
     *
     * @return FileInterface[]
     */
    protected function findAll($prefix = '')
    {
        $filesAndDirs = [];
        $prefix = $this->normalizePath($this->rootPath.'/'.trim($prefix));

        $files = $this->getObjectManager()->getRepository($this->class)->findAll();
        foreach ($files as $file) {
            if (empty($prefix) || false !== strpos($this->getFilePath($file), $prefix)) {
                $filesAndDirs[] = $file;
            }
        }

        if ($this->dirClass) {
            $dirs = $this->getObjectManager()->getRepository($this->dirClass)->findAll();
            foreach ($dirs as $dir) {
                if (empty($prefix) || false !== strpos($this->getFilePath($dir), $prefix)) {
                    $filesAndDirs[] = $dir;
                }
            }
        }

        return $filesAndDirs;
    }

    /**
     * Get filesystem path.
     *
     * Gaufrette uses a key to identify a file or directory. This adapter uses
     * a filesystem path, like /path/to/file/filename.ext, as key.
     *
     * @param MediaInterface $file
     *
     * @return string
     */
    protected function getFilePath(MediaInterface $file)
    {
        return $this->mediaManager->getPath($file);
    }

    /**
     * Map the key to an id to retrieve the file.
     *
     * Gaufrette uses a key to identify a file or directory. This adapter uses
     * a filesystem path, like /path/to/file/filename.ext, as key.
     *
     * @param $key
     *
     * @return string
     */
    protected function mapKeyToId($key)
    {
        return $this->mediaManager->mapPathToId($key);
    }

    /**
     * Computes the key from the specified path.
     *
     * @param string $path
     *
     * return string
     *
     * @return string
     */
    public function computeKey($path)
    {
        $path = $this->normalizePath($path);

        return ltrim(substr($path, strlen($this->directory)), '/');
    }

    /**
     * Computes the path from the specified key.
     *
     * @param string $key The key which for to compute the path
     *
     * @return string A path
     *
     * @throws OutOfBoundsException If the computed path is out of the rootPath
     * @throws RuntimeException     If directory does not exists and cannot be
     *                              created
     */
    protected function computePath($key)
    {
        $this->ensureDirectoryExists($this->rootPath, $this->create);

        return $this->normalizePath($this->rootPath.'/'.$key);
    }

    /**
     * Normalizes the given path.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws OutOfBoundsException If the computed path is out of the
     *                              rootPath
     */
    protected function normalizePath($path)
    {
        $path = Util\Path::normalize($path);

        if (0 !== strpos($path, $this->rootPath)) {
            throw new \OutOfBoundsException(sprintf('The path "%s" is out of the filesystem.', $path));
        }

        return $path;
    }

    /**
     * Get the parent path of a valid absolute path.
     *
     * @param string $path the path to get the parent from
     *
     * @return string the path with the last segment removed
     */
    abstract protected function getParentPath($path);

    /**
     * Get the name from the path.
     *
     * @param string $path a valid absolute path, like /content/jobs/data
     *
     * @return string the name, that is the string after the last "/"
     */
    abstract protected function getBaseName($path);

    /**
     * Set default values for a new file or directory.
     *
     * @param string             $path   Path of the file
     * @param FileInterface      $file
     * @param DirectoryInterface $parent Parent directory of the file
     */
    protected function setFileDefaults($path, FileInterface $file, DirectoryInterface $parent = null)
    {
        $setIdentifier = $this->identifier ? 'set'.ucfirst($this->identifier) : false;
        $name = $this->getBaseName($path);

        if ($setIdentifier) {
            $file->{$setIdentifier}($name);
        }
        $file->setName($name);

        if ($file instanceof HierarchyInterface && $parent && $parent instanceof DirectoryInterface) {
            $file->setParent($parent);
        }
    }

    /**
     * Ensures the specified directory exists, creates it if it does not.
     *
     * @param string $dirPath Path of the directory to test
     * @param bool   $create  Whether to create the directory if it does
     *                        not exist
     *
     * @throws RuntimeException if the directory does not exists and could not
     *                          be created
     */
    protected function ensureDirectoryExists($dirPath, $create = false)
    {
        if (!$this->find($dirPath, true)) {
            if (!$create) {
                throw new \RuntimeException(sprintf('The directory "%s" does not exist.', $dirPath));
            }

            $this->createDirectory($dirPath);
        }
    }

    /**
     * Creates the specified directory and its parents, like mkdir -p.
     *
     * @param string $dirPath Path of the directory to create
     *
     * @return FileInterface The created directory
     *
     * @throws InvalidArgumentException if the directory already exists
     */
    protected function createDirectory($dirPath)
    {
        $parent = null;

        if ($this->isDirectory($dirPath)) {
            throw new \InvalidArgumentException(sprintf(
                'The directory \'%s\' already exists.',
                $dirPath
            ));
        }

        // create parent directory if needed
        $parentPath = $this->getParentPath($dirPath);
        if (!$this->isDirectory($parentPath)) {
            $parent = $this->createDirectory($parentPath);
        }

        $dirClass = $this->dirClass ? $this->dirClass : $this->class;

        $dir = new $dirClass();
        $this->setFileDefaults($dirPath, $dir, $parent);

        $this->getObjectManager()->persist($dir);
        if ($this->flush) {
            $this->getObjectManager()->flush();
        }

        return $dir;
    }
}
