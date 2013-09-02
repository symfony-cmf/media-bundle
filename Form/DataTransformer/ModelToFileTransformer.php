<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Media\BinaryInterface;
use Media\FileInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModelToFileTransformer implements DataTransformerInterface
{
    private $dataClass;

    public function __construct($class)
    {
        if (!is_subclass_of($class, 'Media\FileInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not implement Media\FileInterface',
                $class
            ));
        }

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

        /** @var $file FileInterface */
        $file = new $this->dataClass;
        $file->copyContentFromFile($uploadedFile);

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