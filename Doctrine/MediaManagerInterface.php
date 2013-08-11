<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine;

use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;

/**
 * Interface containing media helper methods, these are probably persistance
 * layer specific.
 *
 * This is to be kept compatible with the SonataMediaBundle
 * MediaProviderInterface to allow integration with sonata.
 */
interface MediaManagerInterface
{
    /**
     * Get path, like:
     * - /path/to/file/filename.ext
     * - /fileId
     *
     * It is similar to a filesystem path only always uses "/" to separate
     * parents, and therefore allows to get the parent from the path.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getPath(MediaInterface $media);

    /**
     * Get an url safe path
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getUrlSafePath(MediaInterface $media);

    /**
     * Create and add a path to the media object if needed;
     * is used fe. by Doctrine PHPCR to generate a unique id.
     *
     * @param MediaInterface $media
     *
     * @return void
     *
     * @throws \RuntimeException if the path could not be created
     */
    public function createPath(MediaInterface $media, $rootPath = null);

    /**
     * Map the path to an id that can be used to lookup the file in the
     * Doctrine store.
     *
     * @param string $path
     * @param string $rootPath
     *
     * @return string
     *
     * @throws \OutOfBoundsException if the path is out of the root path where
     *                              the filesystem is located
     */
    public function mapPathToId($path, $rootPath = null);

    /**
     * Map the requested path (ie. subpath in the URL) to an id that can
     * be used to lookup the file in the Doctrine store.
     *
     * @param string $path
     * @param string $rootPath
     *
     * @return string
     *
     * @throws \OutOfBoundsException if the path is out of the root path where
     *                              the filesystem is located
     */
    public function mapUrlSafePathToId($path, $rootPath = null);
}