<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Adapter\LiipImagine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Imagine\Data\Loader\AbstractDoctrineLoader;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSytemInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Cmf doctrine media loader
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path, set "fullPathId" to true.
 * For ORM the file path concatenates the directory identifiers with '/'
 * and ends with the file identifier.
 */
class CmfMediaDoctrineLoader extends AbstractDoctrineLoader
{
    /**
     * Constructor.
     *
     * @param ImagineInterface $imagine
     * @param ManagerRegistry  $registry
     * @param string           $managerName
     * @param string           $class      fully qualified class name of image
     * @param boolean          $fullPathId whether the identifier contains the
     *                                     full file path (default FALSE)
     */
    public function __construct(
        ImagineInterface $imagine,
        ManagerRegistry $registry,
        $managerName,
        $class = null,
        $fullPathId = false)
    {
        $manager = $registry->getManager($managerName);

        parent::__construct($imagine, $manager, $class);

        $this->fullPathId = $fullPathId;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapPathToId($path)
    {
        if ($this->fullPathId) {
            // The path is being the id
            return substr($path, 0, 1) === '/' ? $path : '/'.$path;
        } else {
            // Get filename component of path, that is the id
            return basename($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getStreamFromImage($image)
    {
        if (!$image instanceof ImageInterface) {
            throw new UnsupportedMediaTypeHttpException(
                sprintf('Source image class "%s" does not implement "%s"',
                    get_class($image),
                    'Symfony\Cmf\Bundle\MediaBundle\ImageInterface'
                )
            );
        }

        /** @var $image ImageInterface */
        if ($image instanceof BinaryInterface) {
            return $image->getContentAsStream();
        } elseif($image instanceof FileSytemInterface) {
            return fopen($image->getFileSystemPath(), 'rb');
        }

        $stream = fopen('php://memory', 'rwb+');
        fwrite($stream, $image->getContentAsString());
        rewind($stream);

        // TODO - error stream is empty:
        // "An image could not be created from the given input"
        // Imagine->load('')

        return $stream;
    }
}
