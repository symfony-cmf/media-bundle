<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Adapter\LiipImagine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Imagine\Data\Loader\AbstractDoctrineLoader;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;
use Symfony\Cmf\Bundle\MediaBundle\Helper\MediaHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Cmf doctrine media loader
 *
 * The path to a file is: /path/to/file/filename.ext
 */
class CmfMediaDoctrineLoader extends AbstractDoctrineLoader
{
    protected $mediaHelper;

    /**
     * Constructor.
     *
     * @param ImagineInterface     $imagine
     * @param ManagerRegistry      $registry
     * @param string               $managerName
     * @param MediaHelperInterface $mediaHelper
     * @param string               $class       fully qualified class name of image
     */
    public function __construct(
        ImagineInterface $imagine,
        ManagerRegistry $registry,
        $managerName,
        MediaHelperInterface $mediaHelper,
        $class = null)
    {
        $manager = $registry->getManager($managerName);

        parent::__construct($imagine, $manager, $class);

        $this->mediaHelper = $mediaHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapPathToId($path)
    {
        return $this->mediaHelper->mapPathToId($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStreamFromImage($image)
    {
        if (!$image instanceof ImageInterface) {
            $type = is_object($image) ? get_class($image) : gettype($image);
            throw new UnsupportedMediaTypeHttpException(
                sprintf('Source image of type "%s" does not implement "%s"',
                    $type,
                    'Symfony\Cmf\Bundle\MediaBundle\ImageInterface'
                )
            );
        }

        /** @var $image ImageInterface */
        if ($image instanceof BinaryInterface) {
            return $image->getContentAsStream();
        }
        if($image instanceof FileSystemInterface) {
            return fopen($image->getFileSystemPath(), 'rb');
        }

        $stream = fopen('php://memory', 'rwb+');
        fwrite($stream, $image->getContentAsString());
        rewind($stream);

        return $stream;
    }
}
