<?php

namespace Symfony\Cmf\Bundle\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * A listener to invalidate the imagine cache when Image documents are modified
 */
class PhpcrImagineCacheInvalidatorSubscriber extends AbstractImagineCacheInvalidatorSubscriber
{
    /**
     * {@inheritdoc}
     */
    protected function getPath(FileInterface $file)
    {
        return $file->getId();
    }
}
