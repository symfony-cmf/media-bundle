<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModelToFileTransformer implements DataTransformerInterface
{
    /**
     * @var UploadFileHelperInterface
     */
    private $helper;

    /**
     * @var
     */
    private $class;

    /**
     * @param UploadFileHelperInterface $helper
     * @param string                    $class
     */
    public function __construct(UploadFileHelperInterface $helper, $class = null)
    {
        $this->helper = $helper;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile) {
            return $uploadedFile;
        }

        try {
            return $this->helper->handleUploadedFile($uploadedFile, $this->class);
        } catch (UploadException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function transform($file)
    {
        return $file;
    }
}
