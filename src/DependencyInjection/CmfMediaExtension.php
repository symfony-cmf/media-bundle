<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\DependencyInjection;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\DoctrineImageDimensionsSubscriber;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\DoctrineStreamRewindSubscriber;
use Symfony\Cmf\Bundle\MediaBundle\Templating\Helper\CmfMediaHelper;
use Symfony\Cmf\Bundle\MediaBundle\Controller\ImageController;
use Symfony\Cmf\Bundle\MediaBundle\Controller\FileController;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CmfMediaExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all Bundles
        $bundles = $container->getParameter('kernel.bundles');

        // process the configuration of CmfMediaExtension
        $configs      = $container->getExtensionConfig($this->getAlias());
        $parameterBag = $container->getParameterBag();
        $configs      = $parameterBag->resolveValue($configs);
        $config       = $this->processConfiguration(new Configuration(), $configs);

        if ($config['persistence']['phpcr']['enabled'] && isset($bundles['CmfCreateBundle'])) {
            $config = [
                'persistence' => [
                    'phpcr' => [
                        'image' => [
                            'enabled'     => true,
                            'model_class' => $config['persistence']['phpcr']['image_class'],
                            'basepath'    => $config['persistence']['phpcr']['media_basepath'],
                        ],
                    ],
                ],
            ];
            $container->prependExtensionConfig('cmf_create', $config);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        // detect bundles
        $bundles    = $container->getParameter('kernel.bundles');
        $useImagine = true === $config['use_imagine']
            || ('auto' === $config['use_imagine']
                && isset($bundles['LiipImagineBundle'])
            )
        ;

        $useJmsSerializer = true === $config['use_jms_serializer']
            || ('auto' === $config['use_jms_serializer']
                && isset($bundles['JMSSerializerBundle'])
            )
        ;

        $useElFinder = true === $config['use_elfinder']
            || ('auto' === $config['use_elfinder']
                && isset($bundles['FMElfinderBundle'])
            )
        ;

        // load config
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container, $useImagine, $useJmsSerializer, $useElFinder);
        }

        $container->setParameter($this->getAlias().'.upload_file_role', $config['upload_file_role']);

        if (isset($config['upload_file_helper_service_id'])) {
            $container->setAlias($this->getAlias().'.upload_file_helper', $config['upload_file_helper_service_id']);
        }
        if (isset($config['upload_image_helper_service_id'])) {
            $container->setAlias($this->getAlias().'.upload_image_helper', $config['upload_image_helper_service_id']);
        }

        if ($useElFinder) {
            $container->setParameter($this->getAlias().'.default_browser', 'elfinder');
        }

        // load general liip imagine configuration
        $this->loadLiipImagine($useImagine, $config, $loader, $container, $useElFinder);
    }

    public function loadPhpcr(
        $config,
        YamlFileLoader $loader,
        ContainerBuilder $container,
        $useImagine,
        $useJmsSerializer,
        $useElFinder
    ) {
        $container->setParameter($this->getAlias().'.backend_type_phpcr', true);
        $prefix = $this->getAlias().'.persistence.phpcr';

        $keys = [
            'media_class'     => 'media.class',
            'file_class'      => 'file.class',
            'directory_class' => 'directory.class',
            'image_class'     => 'image.class',
            'media_basepath'  => 'media_basepath',
            'manager_name'    => 'manager_name',
        ];

        foreach ($keys as $sourceKey => $targetKey) {
            if (isset($config[$sourceKey])) {
                $container->setParameter(
                    $prefix.'.'.$targetKey,
                    $config[$sourceKey]
                );
            }
        }

        if (!isset($config['manager_name'])) {
            $container->setParameter($prefix.'.manager_name', null);
        }

        // load phpcr specific configuration
        $loader->load('persistence-phpcr.yml');
        if (!interface_exists(TokenStorageInterface::class)) {
            $container->getDefinition(FileController::class)->replaceArgument(7, new Reference('security.context'));
            $container->getDefinition(ImageController::class)->replaceArgument(7, new Reference('security.context'));
        }

        // aliases
        $container->setAlias($this->getAlias().'.upload_file_helper', $prefix.'.upload_file_helper');
        $container->setAlias($this->getAlias().'.upload_image_helper', $prefix.'.upload_image_helper');

        if (!$config['event_listeners']['stream_rewind']) {
            $container->removeDefinition(DoctrineStreamRewindSubscriber::class);
        }
        if (!$config['event_listeners']['image_dimensions']) {
            $container->removeDefinition(DoctrineImageDimensionsSubscriber::class);
        } elseif ($useImagine) {
            $definition = $container->getDefinition(DoctrineImageDimensionsSubscriber::class);
            $definition->addArgument(new Reference('liip_imagine'));
        } elseif (!\function_exists('imagecreatefromstring')) {
            throw new InvalidConfigurationException('persistence.phpcr.subscriber.image_dimensions must be set to false if Imagine is not enabled and the GD PHP extension is not available.');
        }

        if ($useImagine) {
            // load phpcr specific imagine configuration
            $loader->load('adapter-imagine-phpcr.yml');
            if (false !== $config['event_listeners']['imagine_cache']) {
                $loader->load('persistence-phpcr-event-imagine.yml');
            }

            // TODO: this should not be phcpr specific but the MediaManagerInterface service should be an alias instead
            $definition = $container->getDefinition(CmfMediaHelper::class);
            $definition->addArgument(new Reference('liip_imagine.templating.helper'));
        } elseif (true === $config['event_listeners']['imagine_cache']) {
            throw new InvalidConfigurationException('persistence.phpcr.event_listeners.imagine_cache may not be forced enabled if Imagine is not enabled.');
        }

        if ($useJmsSerializer) {
            // load phpcr specific serializer configuration
            $loader->load('serializer-phpcr.yml');
        }

        if ($useElFinder) {
            // load phpcr specific elfinder configuration
            $loader->load('adapter-elfinder-phpcr.yml');
        }
    }

    public function loadLiipImagine($enabled, $config, YamlFileLoader $loader, ContainerBuilder $container, $useElFinder)
    {
        if (!$enabled) {
            $container->setParameter($this->getAlias().'.use_imagine', false);
            $container->setParameter($this->getAlias().'.imagine.filter.upload_thumbnail', false);
            $container->setParameter($this->getAlias().'.imagine.filter.elfinder_thumbnail', false);
            $container->setParameter($this->getAlias().'.imagine.all_filters', []);

            return;
        }

        $filters = isset($config['extra_filters']) && \is_array($config['extra_filters'])
            ? array_merge($config['imagine_filters'], $config['extra_filters'])
            : [];
        if (!$useElFinder) {
            unset($filters['elfinder_thumbnail']);
        }
        if ($key = array_search(null, $filters, true)) {
            throw new InvalidConfigurationException("Imagine filter name for $key may not be null");
        }

        $container->setParameter($this->getAlias().'.use_imagine', true);
        $container->setParameter($this->getAlias().'.imagine.filter.upload_thumbnail', $config['imagine_filters']['upload_thumbnail']);
        $container->setParameter($this->getAlias().'.imagine.filter.elfinder_thumbnail', $config['imagine_filters']['elfinder_thumbnail']);
        $container->setParameter($this->getAlias().'.imagine.all_filters', $filters);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath(): string
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace(): string
    {
        return 'http://cmf.symfony.com/schema/dic/media';
    }
}
