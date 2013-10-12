<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;

/**
 * A listener that ensures that BinaryInterface documents persisted to Doctrine
 * have their stream cursor at the beginning of the stream, in case the stream
 * was read before flushing.
 *
 * @author Roel Sint
 * @author David Buchmann <mail@davidbu.ch>
 */
class DoctrineStreamRewindSubscriber implements EventSubscriber
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
        $this->rewindStream($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->rewindStream($args);
    }

    /**
     * Rewind stream of a BinaryInterface.
     *
     * @param LifecycleEventArgs $args
     */
    public function rewindStream(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof BinaryInterface) {
            return;
        }

        $stream = $object->getContentAsStream();
        if (! is_resource($stream)) {
            return;
        }

        rewind($stream);
    }
}