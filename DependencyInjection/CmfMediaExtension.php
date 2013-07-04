<?php

namespace Symfony\Cmf\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CmfMediaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!in_array(strtolower($config['manager_registry']), array('doctrine_orm', 'doctrine_phpcr'))) {
            throw new \InvalidArgumentException(sprintf('CmfMediaBundle - Invalid manager registry "%s".', $config['manager_registry']));
        }

        $container->setParameter($this->getAlias() . '.media_basepath', $config['media_basepath']);
        $container->setParameter($this->getAlias() . '.manager_registry', $config['manager_registry']);
        $container->setParameter($this->getAlias() . '.manager_name', $config['manager_name']);

        if (isset($config['media_class'])) {
            $container->setParameter($this->getAlias() . '.media_class', $config['media_class']);
        }

        if (isset($config['file_class'])) {
            $container->setParameter($this->getAlias() . '.file_class', $config['file_class']);
        }

        if (isset($config['directory_class'])) {
            $container->setParameter($this->getAlias() . '.directory_class', $config['directory_class']);
        }

        if (isset($config['image_class'])) {
            $container->setParameter($this->getAlias() . '.image_class', $config['image_class']);
        }

        $loader->load($config['manager_registry'] . '.xml');

        $this->loadDefaultClasses($config, $container);

        $this->loadLiipImagine($config, $loader, $container);
    }

    public function loadDefaultClasses($config, ContainerBuilder $container)
    {
        switch ($config['manager_registry']) {
            case 'doctrine_phpcr':
                if (!isset($config['media_class'])) {
                    $container->setParameter($this->getAlias() . '.media_class',
                        'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Media'
                    );
                }

                if (!isset($config['file_class'])) {
                    $container->setParameter($this->getAlias() . '.file_class',
                        'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File'
                    );
                }

                if (!isset($config['image_class'])) {
                    $container->setParameter($this->getAlias() . '.image_class',
                        'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image'
                    );
                }
                break;
        }
    }

    public function loadLiipImagine($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $filter = isset($config['imagine_filter'])
            ? $config['imagine_filter']
            : false;
        $filters = isset($config['extra_filters']) && is_array($config['extra_filters'])
            ? array_merge(array($filter), $config['extra_filters'])
            : array();
        $container->setParameter('cmf_media.imagine.filter', $filter);
        $container->setParameter('cmf_media.imagine.all_filters', $filters);

        $bundles = $container->getParameter('kernel.bundles');
        if ('auto' === $config['use_liip_imagine'] && !isset($bundles['LiipImagineBundle'])) {
            $container->removeDefinition('cmf_media.subscriber.imagine_cache');

            return;
        }

        $loader->load($config['manager_registry'] . '.imagine.xml');
    }
}
