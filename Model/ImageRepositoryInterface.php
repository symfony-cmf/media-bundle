<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Model;

/**
 * Interface for doctrine image repositories.
 *
 * It defines common used methods to efficiently retrieve images.
 */
interface ImageRepositoryInterface
{
    /**
     * Set the root path were the file system is located;
     * if not called, the default root path will be used.
     *
     * @param string $rootPath
     *
     * @return self
     */
    public function setRootPath($rootPath);

    /**
     * Get images by name;
     * searches by name and description.
     *
     * @param string $name
     * @param int    $offset
     * @param int    $limit
     *
     * @return mixed
     */
    public function getImagesByName($name, $offset, $limit);
}