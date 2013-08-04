<?php

namespace Symfony\Cmf\Bundle\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EditorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $tags = $container->findTaggedServiceIds('cmf_media.editor.helper');

        if (count($tags) > 0 && $container->hasDefinition('cmf_media.file_upload.helper')) {
            $manager = $container->getDefinition('cmf_media.file_upload.helper');

            foreach ($tags as $id => $tag) {
                $manager->addMethodCall('addEditorHelper', array($tag[0]['alias'], new Reference($id)));
            }
        }
    }
}
