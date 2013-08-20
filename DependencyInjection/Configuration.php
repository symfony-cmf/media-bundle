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
                    ->children()
                        ->arrayNode('phpcr')
                            ->children()
                                ->scalarNode('enabled')->defaultNull()->end()
                                ->scalarNode('media_basepath')->defaultValue('/cms/media')->end()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('media_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Media')->end()
                                ->scalarNode('file_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File')->end()
                                ->scalarNode('directory_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory')->end()
                                ->scalarNode('image_class')->defaultValue('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('upload_file_role')->defaultValue('ROLE_CAN_UPLOAD_FILE')->end()

                ->enumNode('use_jms_serializer')
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
            ->fixXmlConfig('extra_filter')
            ->children()
                ->enumNode('use_imagine')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
                ->scalarNode('imagine_filter')->defaultValue('image_upload_thumbnail')->end()
                ->arrayNode('extra_filters')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
