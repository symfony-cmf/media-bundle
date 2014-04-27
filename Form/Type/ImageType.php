<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Form\Type;

use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer\ModelToFileTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    private $dataClass;
    private $uploadFileHelper;
    private $useImagine;
    private $defaultFilter;

    /**
     * @param string                    $class
     * @param UploadFileHelperInterface $uploadFileHelper
     * @param bool                      $useImagine
     * @param bool                      $defaultFilter
     */
    public function __construct($class, UploadFileHelperInterface $uploadFileHelper, $useImagine = false, $defaultFilter = false)
    {
        $this->dataClass = $class;
        $this->uploadFileHelper = $uploadFileHelper;
        $this->useImagine = $useImagine;
        $this->defaultFilter = $this->useImagine ? $defaultFilter : false;
    }

    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'cmf_media_image';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ModelToFileTransformer($this->uploadFileHelper, $options['data_class']);
        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['imagine_filter'] = $this->useImagine ? $options['imagine_filter'] : false;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'data_class' => $this->dataClass,
            'imagine_filter' => $this->defaultFilter,
        ));
    }
}
