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
                ->enumNode('manager_registry')
                    ->values(array('doctrine_orm', 'doctrine_phpcr'))
                    ->defaultValue('doctrine_phpcr')
                ->end()
                ->scalarNode('manager_name')->defaultValue('default')->end()
                ->scalarNode('media_basepath')->defaultValue('/cms/media')->end()
                ->scalarNode('media_class')->defaultNull()->end()
                ->scalarNode('file_class')->defaultNull()->end()
                ->scalarNode('directory_class')->defaultNull()->end()
                ->scalarNode('image_class')->defaultNull()->end()
            ->end()
        ;

        $this->addImageSection($rootNode);

        return $treeBuilder;
    }

    private function addImageSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->enumNode('use_liip_imagine')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
                ->scalarNode('imagine_filter')->end()
                ->arrayNode('extra_filters')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
