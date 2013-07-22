<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Adapter\LiipImagine;

/**
 * Cmf doctrine media loader
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path.
 */
class PhpcrCmfMediaDoctrineLoader extends AbstractCmfMediaDoctrineLoader
{
    /**
     * {@inheritdoc}
     */
    protected function mapPathToId($path)
    {
        // The path is being the id
        return substr($path, 0, 1) === '/' ? $path : '/'.$path;
    }
}
