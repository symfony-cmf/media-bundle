<?php

namespace Symfony\Cmf\Bundle\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Imagine\Image\ImagineInterface;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

/**
 * A listener to update the dimensions of an image object from the content.
 */
class DoctrineImageDimensionsSubscriber implements EventSubscriber
{
    protected $useImagine;
    protected $imagine;

    /**
     * @param bool             $useImagine
     * @param ImagineInterface $imagine
     */
    public function __construct($useImagine = false, ImagineInterface $imagine)
    {
        $this->useImagine      = $useImagine;
        $this->imagine         = $imagine;
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

        if ($object instanceof ImageInterface) {
            if ($object->getSize() === 0) {
                $object->setWidth(0);
                $object->setHeight(0);

                return;
            }

            // Determine the with and height of the object from
            // the binary image data
            if ($this->useImagine) {
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
                }
            } elseif (function_exists('imagecreatefromstring')) {
                // use gd to determine the dimensions
                $content = $object->getContentAsString();
                $resource = imagecreatefromstring($content);
                $object->setWidth(imagesx($resource));
                $object->setHeight(imagesy($resource));
            } else {
                $object->setWidth(0);
                $object->setHeight(0);
            }
        }
    }
}