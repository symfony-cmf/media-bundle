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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
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
                                        ->booleanNode('stream_rewind')->defaultTrue()->end()
                                        ->booleanNode('image_dimensions')->defaultTrue()->end()
                                        ->enumNode('imagine_cache')
                                            ->values([true, false, 'auto'])
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
                    ->values([true, false, 'auto'])
                    ->defaultValue('auto')
                ->end()

                ->enumNode('use_elfinder')
                    ->values([true, false, 'auto'])
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
            ->fixXmlConfig('extra_filter')
            ->children()
                ->enumNode('use_imagine')
                    ->values([true, false, 'auto'])
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
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
