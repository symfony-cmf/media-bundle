<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModelToFileTransformer implements DataTransformerInterface
{
    private $dataClass;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        if (!is_subclass_of($class, 'Symfony\Cmf\Bundle\MediaBundle\FileInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'The class "%s" does not implement Symfony\Cmf\Bundle\MediaBundle\FileInterface',
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