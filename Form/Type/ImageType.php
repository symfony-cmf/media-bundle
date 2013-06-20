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
    private $defaultFilter;
    private $dataClass;

    public function __construct($class, $defaultFilter)
    {
        $this->dataClass = $class;
        $this->defaultFilter = $defaultFilter;
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
        $view->vars['imagine_filter'] = $options['imagine_filter'];
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'data_class' => $this->dataClass,
            'imagine_filter' => $this->defaultFilter,
        ));
    }
}
