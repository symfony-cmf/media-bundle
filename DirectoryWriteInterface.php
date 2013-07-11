<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Write interface definition for DirectoryInterface.
 */
interface DirectoryWriteInterface extends DirectoryInterface
{
    /**
     * Set the parent directory.
     *
     * @param DirectoryInterface $parent
     *
     * @return boolean
     */
    public function setParentDirectory(DirectoryInterface $parent);
}