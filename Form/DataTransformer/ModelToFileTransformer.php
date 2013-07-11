<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Symfony\Cmf\Bundle\MediaBundle\FileWriteInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModelToFileTransformer implements DataTransformerInterface
{
    private $dataClass;

    public function __construct($class)
    {
        $this->dataClass = $class;

        if (!is_subclass_of($class, 'Symfony\Cmf\Bundle\MediaBundle\FileWriteInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not implement Symfony\Cmf\Bundle\MediaBundle\FileWriteInterface',
                $class
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile) {
            return $uploadedFile;
        }

        /** @var $file FileWriteInterface */
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