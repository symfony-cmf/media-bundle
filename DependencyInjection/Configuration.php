<?php

namespace Symfony\Cmf\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cmf_media');

        $rootNode
            ->children()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcr')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->fixXmlConfig('event_listener')
                            ->children()
                                ->scalarNode('media_basepath')->defaultValue('/cms/media')->end()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('media_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Media')->end()
                                ->scalarNode('file_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File')->end()
                                ->scalarNode('directory_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory')->end()
                                ->scalarNode('image_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image')->end()
                                ->arrayNode('event_listeners')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('stream_rewind')->defaultTrue()->end()
                                        ->scalarNode('image_dimensions')->defaultTrue()->end()
                                        ->enumNode('imagine_cache')
                                            ->values(array(true, false, 'auto'))
                                            ->defaultValue('auto')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('upload_file_role')->defaultValue('ROLE_CAN_UPLOAD_FILE')->end()

                ->scalarNode('upload_file_helper_service_id')->end()
                ->scalarNode('upload_image_helper_service_id')->end()

                ->enumNode('use_jms_serializer')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()

                ->enumNode('use_elfinder')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()

            ->end()
        ;

        $this->addImageSection($rootNode);

        return $treeBuilder;
    }

    private function addImageSection(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('imagine_filter')
            ->fixXmlConfig('extra_filter')
            ->children()
                ->enumNode('use_imagine')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
                ->arrayNode('imagine_filters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('upload_thumbnail')->defaultValue('image_upload_thumbnail')->end()
                        ->scalarNode('elfinder_thumbnail')->defaultValue('elfinder_thumbnail')->end()
                    ->end()
                ->end()
                ->arrayNode('extra_filters')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
