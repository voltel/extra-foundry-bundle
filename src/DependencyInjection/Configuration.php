<?php

namespace Voltel\ExtraFoundryBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const DATABASE_MYSQL = 'mysql';

    private bool $debug;
    private string $projectDir;

    public function __construct(
        bool $debug,
        string $c_kernel_project_dir
    )
    {
        $this->debug = $debug;
        $this->projectDir = $c_kernel_project_dir;
    }

    // See an example at: "vendor\symfony\framework-bundle\DependencyInjection\Configuration.php"
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('voltel_extra_foundry');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->enumNode('database_type')
                ->values([self::DATABASE_MYSQL])
                ->defaultValue(self::DATABASE_MYSQL)
                ->info(sprintf('Database (persistence layer) type: "%s" is currently the only supported option.', self::DATABASE_MYSQL))
            ->end()
            ->scalarNode('dump_directory_path')
                ->defaultValue(str_replace('//', '/', $this->projectDir . sprintf('/var/%s_dumps', self::DATABASE_MYSQL)))
                ->info('Filesystem path of the directory where database dump files are located.')
            ->end()
            ->scalarNode('dump_file_name')
                ->defaultNull()
                ->info('File name (w/o file path) of database dump file that will be loaded in the current database.')
            ->end()
            ->scalarNode('connection_name')
                ->defaultValue('default')
                ->info('Doctrine connection name to use for data import (data loading).')
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }

    /**
     * voltel: This is an attempt to provide compatibility for versions of Symfony
     *
     * Note: This is just a convenience method.
     * Returns an array with two values: treeBuilder and rootNode
     *
     * @return array
     * @throws \ReflectionException
     */
    private function getRootNode() : array
    {
        $oReflectionClass = new \ReflectionClass(TreeBuilder::class);

        if ($oReflectionClass->hasMethod('__construct')
            && $oReflectionClass->hasMethod('getRootNode')
        ) {
            // Symfony >= 4.3
            $treeBuilder = new TreeBuilder('knpu_lorem_ipsum');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony < 4.3
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('knpu_lorem_ipsum');
        }//endif

        return [$treeBuilder, $rootNode];
    }//end of function

}