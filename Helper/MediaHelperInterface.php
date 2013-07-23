<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Helper;

use Symfony\Cmf\Bundle\MediaBundle\MediaInterface;

/**
 * Interface containing media helper methods, these are probably persistance
 * layer specific.
 *
 * This is to be kept compatible with the SonataMediaBundle
 * MediaProviderInterface to allow integration with sonata.
 */
interface MediaHelperInterface
{
    /**
     * Get filesystem path for file or directory, like:
     * - /path/to/file/filename.ext
     * - /fileId
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getFilePath(MediaInterface $media);

    /**
     * Create and add a filesystem path to the media object if needed;
     * is used fe. by Doctrine PHPCR to generate a unique id.
     *
     * @param MediaInterface $media
     *
     * @return void
     *
     * @throws \RuntimeException if the file path could not be created
     */
    public function createFilePath(MediaInterface $media, $rootPath = null);

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
    public function mapPathToId($path, $rootPath = null);

    /**
     * Get the parent path of a valid absolute path.
     *
     * @param string $path the path to get the parent from
     *
     * @return string the path with the last segment removed
     */
    public function getParentPath($path);

    /**
     * Get the name from the path
     *
     * @param string $path a valid absolute path, like /content/jobs/data
     *
     * @return string the name, that is the string after the last "/"
     */
    public function getBaseName($path);
}