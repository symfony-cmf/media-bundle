<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Form\Type;

use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type which transforms an uploaded file to an object implementing the
 * Symfony\Cmf\Bundle\MediaBundle\ImageInterface.
 *
 * It renders as a file upload button and provides a preview of the uploaded
 * image, if any.
 * To see the preview you can use the twig template provided by this bundle.
 *
 * Usage: you need to supply the object class to which the file will be
 * transformed (which should implement ImageInterface) and an UploadFileHelper,
 * which will handle the UploadedFile and create the transformed object.
 *
 * If the LiipImagineBundle is used in your project, you can configure the imagine
 * filter to use for the preview, as well as additional filters to remove from cache
 * when the image is replaced. If the filter is not specified, it defaults to
 * image_upload_thumbnail.
 */
class ImageType extends FileType
{
    /**
     * @var bool
     */
    private $useImagine;

    /**
     * @var bool
     */
    private $defaultFilter;

    /**
     * @param string                    $class
     * @param UploadFileHelperInterface $uploadFileHelper
     * @param bool                      $useImagine
     * @param bool                      $defaultFilter
     */
    public function __construct($class, UploadFileHelperInterface $uploadFileHelper, $useImagine = false, $defaultFilter = false)
    {
        parent::__construct($class, $uploadFileHelper);
        $this->useImagine = $useImagine;
        $this->defaultFilter = $this->useImagine ? $defaultFilter : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cmf_media_image';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['imagine_filter'] = $this->useImagine ? $options['imagine_filter'] : false;
        $view->vars['use_timestamp'] = $options['use_timestamp'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $options)
    {
        parent::configureOptions($options);
        $options->setDefaults(['imagine_filter' => $this->defaultFilter, 'use_timestamp' => false]);
    }
}
