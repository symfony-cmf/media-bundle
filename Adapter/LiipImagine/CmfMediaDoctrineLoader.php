<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Adapter\LiipImagine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Imagine\Data\Loader\AbstractDoctrineLoader;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * Cmf doctrine media loader
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path, set "fullPathId" to true.
 * For ORM the file path can concatenate the directory identifiers with '/'
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
        // TODO: remove fullPathId config?
        if ($this->fullPathId) {
            // The path is being the id
            return substr($path, 0, 1) === '/' ? $path : '/'.$path;
        } else {
            // Get filename component of path, that is the id
            return substr($path, strrpos($path, '/') + 1);
        }
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
