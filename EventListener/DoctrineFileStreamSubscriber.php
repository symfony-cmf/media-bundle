<?php

namespace Symfony\Cmf\Bundle\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;

/**
 * A listener to ensure the stream cursor of a file object is at the beginning
 * before the object is persisted. This prevents the content to be empty.
 */
class DoctrineFileStreamSubscriber implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->resetStream($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->resetStream($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function resetStream(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof BinaryInterface) {
            $stream = $object->getContentAsStream();
            rewind($stream);
        }
    }
}