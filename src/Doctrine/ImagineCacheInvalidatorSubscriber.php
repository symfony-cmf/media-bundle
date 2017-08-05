<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Document\Resource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * A listener to invalidate the imagine cache when Image documents are
 * modified.
 *
 * This listener is not specific to PHPCR-ODM, but knows about it to support
 * updating a Resource document directly.
 *
 * @author Roel Sint
 * @author David Buchmann <mail@davidbu.ch>
 */
class ImagineCacheInvalidatorSubscriber implements EventSubscriber
{
    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    /**
     * @var CacheManager
     */
    private $manager;

    /**
     * Filter names to invalidate.
     *
     * @var array
     */
    private $filters;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param MediaManagerInterface $mediaManager
     * @param CacheManager          $manager      the imagine cache manager
     *                                            this as otherwise we have a scope problem
     * @param $filters
     *
     * @internal param array $filter list of filter names to invalidate
     */
    public function __construct(MediaManagerInterface $mediaManager, CacheManager $manager, $filters)
    {
        $this->mediaManager = $mediaManager;
        $this->manager = $manager;
        $this->filters = $filters;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'postUpdate',
            'preRemove',
        ];
    }

    /**
     * Invalidate cache after a document was updated.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->invalidateCache($args);
    }

    /**
     * Invalidate the cache when removing an image. Do this before the flush to
     * still have access to the parent of the document.
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->invalidateCache($args);
    }

    /**
     * Check if this could mean an image document was modified (check resource,
     * file and image).
     *
     * @param LifecycleEventArgs $args
     */
    private function invalidateCache(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        // If we hear about the Resource which is nested in a File, we get the
        // parent. instanceof can handle the case where Resource is not a known
        // class - the condition will just be false in that case.
        if ($object instanceof Resource) {
            $object = $object->getParentDocument();
        }
        if (!$object instanceof ImageInterface) {
            return;
        }

        if (null === $this->request) {
            // do not fail on CLI
            return;
        }

        foreach ($this->filters as $filter) {
            $path = $this->manager->resolve($this->mediaManager->getUrlSafePath($object), $filter);
            if ($path instanceof RedirectResponse) {
                $path = $path->getTargetUrl();
            }

            // TODO: this might not be needed https://github.com/liip/LiipImagineBundle/issues/162
            if (false !== strpos($path, $filter)) {
                $path = substr($path, strpos($path, $filter) + strlen($filter));
            }
            $this->manager->remove($path, $filter);
        }
    }
}
