<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Form\Type;

use Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer\ModelToFileTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    private $dataClass;
    private $useImagine;
    private $defaultFilter;

    /**
     * @param string $class
     * @param bool   $useImagine
     * @param bool   $defaultFilter
     */
    public function __construct($class, $useImagine = false, $defaultFilter = false)
    {
        $this->dataClass = $class;
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
        $transformer = new ModelToFileTransformer($this->dataClass);
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
