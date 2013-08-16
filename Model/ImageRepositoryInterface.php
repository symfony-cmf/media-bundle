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
     * Search images by term;
     * searches by name and description.
     *
     * @param string $term
     * @param int    $limit
     * @param int    $offset
     *
     * @return mixed
     */
    public function searchImages($term, $limit = 0, $offset = 0);
}