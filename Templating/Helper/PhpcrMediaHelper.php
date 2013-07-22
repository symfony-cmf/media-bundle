<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Templating\Helper;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;

class PhpcrMediaHelper extends AbstractMediaHelper
{
    /**
     * {@inheritdoc}
     */
    protected function getFilePath(FileInterface $file)
    {
        return $file->getId();
    }
}
