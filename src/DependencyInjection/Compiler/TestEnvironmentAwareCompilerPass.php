<?php


namespace Voltel\ExtraFoundryBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TestEnvironmentAwareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container_builder)
    {
        if (!$container_builder->hasParameter('kernel.environment')) {
            return;
        }//endif

        if ('test' !== $container_builder->getParameter('kernel.environment')) {
            return;
        }//endif

        $definition = $container_builder->getDefinition('voltel_extra_foundry.persist_service');
        $definition->setPublic(true);

        $definition = $container_builder->getDefinition('voltel_extra_foundry.entity_setup');
        $definition->setPublic(true);
    }//end of function

}//end of class
