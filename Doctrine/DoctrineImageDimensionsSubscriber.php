<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Imagine\Image\ImagineInterface;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;

/**
 * A listener to update the dimensions of an image object from the content.
 *
 * This is done using the imagine service if passed into the listener,
 * otherwise GD is used if available.
 *
 * @author Roel Sint
 * @author David Buchmann <mail@davidbu.ch>
 */
class DoctrineImageDimensionsSubscriber implements EventSubscriber
{
    protected $imagine;

    /**
     * @param ImagineInterface $imagine Optional imagine service to use.
     */
    public function __construct(ImagineInterface $imagine = null)
    {
        $this->imagine = $imagine;
    }

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
        $this->updateDimensionsFromContent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->updateDimensionsFromContent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function updateDimensionsFromContent(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Image) {
            return;
        }

        if ($this->imagine) {
            // use imagine to determine the dimensions
            if ($object instanceof BinaryInterface) {
                $stream = $object->getContentAsStream();
                $image = $this->imagine->read($stream);
            } elseif ($object instanceof FileSystemInterface) {
                $image = $this->imagine->open($object->getFileSystemPath());
            } else {
                $image = $this->imagine->load($object->getContentAsString());
            }

            if ($image) {
                $size = $image->getSize();

                $object->setWidth($size->getWidth());
                $object->setHeight($size->getHeight());
            } else {
                $object->setWidth(0);
                $object->setHeight(0);
            }

            return;
        }

        if (function_exists('imagecreatefromstring')) {
            // use gd to determine the dimensions
            $content = $object->getContentAsString();
            $resource = imagecreatefromstring($content);
            if ($resource) {
                $object->setWidth(imagesx($resource));
                $object->setHeight(imagesy($resource));
            } else {
                $object->setWidth(0);
                $object->setHeight(0);
            }
        }

        throw new \RuntimeException('Neither have Imagine nor the gd PHP extension');
    }
}
