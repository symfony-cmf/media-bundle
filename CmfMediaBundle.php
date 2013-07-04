<?php

namespace Symfony\Cmf\Bundle\MediaBundle;

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmfMediaBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (class_exists('Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass')) {
            $container->addCompilerPass(
                DoctrinePhpcrMappingsPass::createXmlMappingDriver(
                    array(
                        realpath(__DIR__ . '/Resources/config/doctrine-model') => 'Symfony\Cmf\Bundle\MediaBundle\Model',
                        realpath(__DIR__ . '/Resources/config/doctrine-phpcr') => 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr',
                    ),
                    array('cmf_media.manager_name')
                )
            );
        }
    }
}
