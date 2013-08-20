<?php

namespace Symfony\Cmf\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CmfMediaExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all Bundles
        $bundles = $container->getParameter('kernel.bundles');

        // process the configuration of CmfMediaExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        $parameterBag = $container->getParameterBag();
        $configs = $parameterBag->resolveValue($configs);
        $config = $this->processConfiguration(new Configuration(), $configs);

        if(!empty($config['persistence']['phpcr']['enabled'])) {
            if (isset($bundles['CmfCreateBundle'])) {
                $config = array(
                    'image' => array(
                        'enabled'     => true,
                        'model_class' => '%cmf_media.persistence.phpcr.image.class%',
                        'basepath'    => '%cmf_media.persistence.phpcr.media_basepath%',
                    ),
                );
                $container->prependExtensionConfig('cmf_create', $config);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // detect bundles
        if ($config['use_imagine'] ||
            ('auto' === $config['use_imagine'] && isset($bundles['LiipImagineBundle']))
        ) {
            $useImagine = true;
        } else {
            $useImagine = false;
        }

        if ($config['use_jms_serializer'] ||
            ('auto' === $config['use_jms_serializer'] && isset($bundles['JMSSerializerBundle']))
        ) {
            $useJmsSerializer = true;
        } else {
            $useJmsSerializer = false;
        }

        // load config
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!empty($config['persistence']['phpcr']['enabled'])) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container, $useImagine, $useJmsSerializer);
        }

        $container->setParameter($this->getAlias() . '.upload_file_role', $config['upload_file_role']);

        // load general liip imagine configuration
        $this->loadLiipImagine($useImagine, $config, $loader, $container);
    }

    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container, $useImagine, $useJmsSerializer)
    {
        $container->setParameter($this->getAlias() . '.backend_type_phpcr', true);

        $keys = array(
            'media_class' => 'media.class',
            'file_class' => 'file.class',
            'directory_class' => 'directory.class',
            'image_class' => 'image.class',
            'media_basepath' => 'media_basepath',
            'manager_name' => 'manager_name',
        );

        foreach ($keys as $sourceKey => $targetKey) {
            if (isset($config[$sourceKey])) {
                $container->setParameter(
                    $this->getAlias() . '.persistence.phpcr.' . $targetKey,
                    $config[$sourceKey]
                );
            }
        }

        // load phpcr specific configuration
        $loader->load('persistence-phpcr.xml');

        if ($useImagine) {
            // load phpcr specific imagine configuration
            $loader->load('imagine-persistence-phpcr.xml');
        }

        if ($useJmsSerializer) {
            // load phpcr specific serializer configuration
            $loader->load('serializer-persistence-phpcr.xml');
        }
    }

    public function loadLiipImagine($enabled, $config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        if ($enabled) {
            $container->setParameter($this->getAlias() . '.use_imagine', false);
            $container->setParameter($this->getAlias() . '.imagine.filter', false);
            $container->setParameter($this->getAlias() . '.imagine.all_filters', array());

            return;
        }

        $filters = isset($config['extra_filters']) && is_array($config['extra_filters'])
            ? array_merge(array($config['imagine_filter']), $config['extra_filters'])
            : array();

        $container->setParameter($this->getAlias() . '.use_imagine', true);
        $container->setParameter($this->getAlias() . '.imagine.filter', $config['imagine_filter']);
        $container->setParameter($this->getAlias() . '.imagine.all_filters', $filters);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/media';
    }
}
