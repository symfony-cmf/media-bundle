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

use PHPCR\Util\PathHelper;

class PhpcrCmfMediaDoctrine extends AbstractCmfMediaDoctrine
{
    /**
     * {@inheritdoc}
     */
    protected function getParentPath($path)
    {
        return PathHelper::getParentPath($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseName($path)
    {
        return PathHelper::getNodeName($path);
    }
}
