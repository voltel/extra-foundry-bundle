<?php

namespace Voltel\ExtraFoundryBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Voltel\ExtraFoundryBundle\DependencyInjection\VoltelExtraFoundryExtension;
use Voltel\ExtraFoundryBundle\DependencyInjection\Compiler\TestEnvironmentAwareCompilerPass;

class VoltelExtraFoundryBundle extends Bundle
{
    // This class will add compiler passes to the container,
    // and return a container extension with configuration

    /**
     * Note: adds compiler pass ("WordProviderCompilerPass") to locate all tagged services and
     * add an array of references to them as an argument to the definition of bundle service(s).
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // There are different types of compiler passes,
        // which determine when they are executed relative to other passes.
        // And, there's also a priority.
        $container->addCompilerPass(new TestEnvironmentAwareCompilerPass());
    }

    /**
     * voltel: this overrides the parent implementation of this method
     */
    public function getContainerExtension()
    {
        if (is_null($this->extension)) {
            $this->extension = new VoltelExtraFoundryExtension();
        }
        return $this->extension;
    }

}