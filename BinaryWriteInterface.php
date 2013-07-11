<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Write interface definition for BinaryInterface.
 */
interface BinaryWriteInterface extends BinaryInterface
{
    /**
     * @param $stream
     */
    public function setContentFromStream($stream);
}