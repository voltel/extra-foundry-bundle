<?php


namespace Voltel\ExtraFoundryBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Voltel\ExtraFoundryBundle\Service\FixtureLoad\LoadDumpFromDatabaseInterface;

class VoltelExtraFoundryExtension extends Extension
{
    /**
     * Note: The container builder is empty at the beginning of this method.
     * There are no services in it, so we can't use "findTaggedServiceIds()" method to locate tagged services.
     * See the Bundle class for compiler pass.
     *
     * Note: This container only has the parameters from the actual container.
     *
     * Note: The invocation of "processConfiguration()" method inside
     * will return one aggregated config for this environment from all located configs (array of arrays),
     * and it will be validated by rules of Configuration class.
     *
     * @param array $configs
     * @param ContainerBuilder $container_builder
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container_builder)
    {
        // voltel: This "strange" code is required to loadSqlDump the config from "config/services.xml" file
        $loader = new XmlFileLoader($container_builder, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');

        //$container_builder->setDefinition()

        // Read at: https://symfony.com/doc/current/bundles/configuration.html#processing-the-configs-array
        // $configuration_tree_rules = new Configuration();
        $configuration_tree_rules = $this->getConfiguration($configs, $container_builder);
        $a_config_options = $this->processConfiguration($configuration_tree_rules, $configs);

        $definition = $container_builder->getDefinition('voltel_extra_foundry.mysql_loader');
        $definition->setArgument(1, $a_config_options['dump_directory_path']);

        $definition = $container_builder->getDefinition('voltel_extra_foundry.sql_loader');
        $definition->setArgument(1, $a_config_options['database_type']);
        $definition->setArgument(2, $a_config_options['dump_file_name']);
        $definition->setArgument(3, $a_config_options['connection_name']);

        // This will later result in a tag "voltel_extra_foundry.database_loader"
        // being added to all services in the container which implement interface "LoadDumpFromDatabaseInterface".
        // This tag is used by compiler pass to gather references to tagged services and modify our service definition.
        $container_builder->registerForAutoconfiguration(LoadDumpFromDatabaseInterface::class)
            ->addTag('voltel_extra_foundry.database_dump_loader');

        // Add classes with annotations to be compiled.
        // Read at: https://symfony.com/doc/current/bundles/extension.html#adding-classes-to-compile
        //$this->addAnnotatedClassesToCompile([/**/])
    }//end of function


    /**
     * Read at: https://symfony.com/doc/current/configuration/using_parameters_in_dic.html
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        // custom argument to my class constructor - boolean indicating a "debug" mode
        $l_debug = (bool) $container->getParameter('kernel.debug');
        // custom argument to my class constructor - boolean indicating a "debug" mode
        $c_project_dir = $container->getParameter('kernel.project_dir');

        return new Configuration($l_debug, $c_project_dir);
    }

    /**
     * This will be used as an alias for the bundle, as shown e.g. by running in terminal "php bin/console config:dump"
     * Actually, this is the same value that the parent implementation would produce by default.
     *  ---------------------------- ------------------------
     *   Bundle name                  Extension alias
     *  ---------------------------- ------------------------
     *   VoltelExtraFoundryBundle     voltel_extra_foundry
     *
     * @return string
     */
    public function getAlias()
    {
        return 'voltel_extra_foundry';
    }
}