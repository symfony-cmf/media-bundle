<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
