<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModelToFileTransformer implements DataTransformerInterface
{
    private $dataClass;

    public function __construct($class)
    {
        $this->dataClass = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile) {
            return $uploadedFile;
        }

        /** @var $uploadedFile UploadedFile */
        $stream = fopen($uploadedFile->getPathname(), 'rb');
        if (! $stream) {
            throw new \RuntimeException("File '$uploadedFile->getPathname()' not found");
        }

        /** @var $file FileInterface */
        $file = new $this->dataClass;
        if ($file instanceof BinaryInterface) {
            $file->setContentFromStream($stream);
        } else {
            $file->setContentFromString(stream_get_contents($stream));
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($file)
    {
        return $file;
    }
}