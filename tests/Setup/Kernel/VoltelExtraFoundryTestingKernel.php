<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Kernel;


use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\AddressFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CategoryFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CustomerFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderItemFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\CustomerStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\OrderStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\ProductStory;
use Voltel\ExtraFoundryBundle\VoltelExtraFoundryBundle;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

class VoltelExtraFoundryTestingKernel extends Kernel
{
    public function __construct(string $c_environment = 'test', bool $l_debug = false)
    {
        parent::__construct($c_environment, $l_debug);
    }


    public function registerBundles()
    {
        return [
            new DoctrineBundle(),
            new VoltelExtraFoundryBundle(),
            new ZenstruckFoundryBundle(),
            new FrameworkBundle(), // w/o this bundle I have an exception of: a non-existent service "annotation_reader"
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            // Services that are used in tests
            $container->loadFromExtension('voltel_extra_foundry', [
                'database_type' => 'mysql',
                'dump_directory_path' => __DIR__ . '/../MySqlDump',
                'dump_file_name' => 'mysql_dump_for_tests.sql',
                'connection_name' => 'default'
            ]);

            $container->setAlias('test.voltel_extra_foundry.persist_service', 'voltel_extra_foundry.persist_service')
                ->setPublic(true);
            $container->setAlias('test.voltel_extra_foundry.entity_setup', 'voltel_extra_foundry.entity_setup')
                ->setPublic(true);
            $container->setAlias('test.voltel_extra_foundry.sql_loader', 'voltel_extra_foundry.sql_loader')
                ->setPublic(true);
            //
            $container->register('voltel.product_slug_provider', 'Voltel\ExtraFoundryBundle\Tests\Setup\Service\ProductSlugProviderService')
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->setPublic(true)
            ;

            //$container->loadFromExtension('framework', []);

            //<editor-fold desc="Localized faker services">
            $container->register('test.voltel_extra_foundry.faker_us', Generator::class)
                ->setFactory([Factory::class, 'create'])
                ->setArgument(0, 'en_US')
                ->setPublic(true) // is used in tests, accessed via self::$container
            ;
            //
            $container->register('test.voltel_extra_foundry.faker_ua', Generator::class)
                ->setFactory([Factory::class, 'create'])
                ->setArgument(0, 'uk_UA')
            ;
            //
            $container->register('test.voltel_extra_foundry.faker_ru', Generator::class)
                ->setFactory([Factory::class, 'create'])
                ->setArgument(0, 'ru_RU')
            ;
            //</editor-fold>

            //<editor-fold desc="Configure model factories">
            $container->register('test.category_factory', CategoryFactory::class)
                ->setArgument(0, new Reference('test.voltel_extra_foundry.faker_ua'))
                ->addTag('foundry.factory')
            ;

            $container->register('test.product_factory', ProductFactory::class)
                ->setArgument(0, new Reference('voltel.product_slug_provider'))
                ->addTag('foundry.factory')
            ;

            $container->register('test.address_factory', AddressFactory::class)
                ->setArguments([
                    new Reference('test.voltel_extra_foundry.faker_us'),
                    new Reference('test.voltel_extra_foundry.faker_ua'),
                    new Reference('test.voltel_extra_foundry.faker_ru'),
                ])
                ->addTag('foundry.factory')
            ;

            $container->register('test.customer_factory', CustomerFactory::class)
                ->setArguments([
                    new Reference('test.voltel_extra_foundry.faker_us'),
                    new Reference('test.voltel_extra_foundry.faker_ua'),
                    new Reference('test.voltel_extra_foundry.faker_ru'),
                ])
                ->addTag('foundry.factory')
            ;

            $container->register('test.order_factory', OrderFactory::class)
                ->addTag('foundry.factory')
            ;

            $container->register('test.order_factory', OrderItemFactory::class)
                ->addTag('foundry.factory')
            ;
            //</editor-fold>

            //<editor-fold desc="Configure foundry stories">
            $container->register('test.product_story', ProductStory::class)
                ->addMethodCall('injectEntityProxyPersistService', [
                    new Reference('voltel_extra_foundry.persist_service'),
                ])
                ->setPublic(true)
            ;

            $container->register('test.customer_story', CustomerStory::class)
                ->addMethodCall('injectEntityProxyPersistService', [
                    new Reference('voltel_extra_foundry.persist_service'),
                ])
                ->setPublic(true)
            ;

            $container->register('test.order_story', OrderStory::class)
                ->addMethodCall('injectEntityProxyPersistService', [
                    new Reference('voltel_extra_foundry.persist_service'),
                ])
                ->setPublic(true)
            ;
            //</editor-fold>

            // Since v1.9.0, explicit configuration of "auto_refresh_proxies" (will default to "true" in v2.0) is mandatory
            $container->loadFromExtension('zenstruck_foundry', [
                'auto_refresh_proxies' => true,
            ]);

            //<editor-fold desc="Configure Doctrine">
            //echo PHP_EOL . 'Database URL: ' . $_ENV['DATABASE_URL'];
            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'default_connection' => 'default',
                    'connections' => [
                        'default' => [
                            //'url' => 'mysql://testuser:password@127.0.0.1:3306/voltel_extra_foundry_test?serverVersion=5.7',
                            'url' => $_ENV['DATABASE_URL'],
                            'logging' => false,
                            'override_url' => true, // the only supported value. Used here to avoid deprecation notice
                            ]
                        ],
                    ],
                'orm' => [
                    'default_entity_manager' => 'default',
                    //'auto_generate_proxy_classes' => true,
                    'entity_managers' => [
                        'default' => [
                            'connection' => 'default',
                            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                            //'auto_mapping' => true,
                            'mappings' => [
                                'App' => [
                                    'is_bundle' => false,
                                    'type' => 'annotation',
                                    'dir' => __DIR__ . '/../Entity',
                                    'prefix' => 'Voltel\ExtraFoundryBundle\Tests\Setup\Entity',
                                    'alias' => 'Test'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            //</editor-fold>

        });
    }


}//end of class

//class MyCompilerPass implements CompilerPassInterface
//{
//    public function process(ContainerBuilder $container)
//    {
//        //$container->getDefinition('doctrine')->setPublic(true);
//       //$container->getDefinition('annotation_reader')->setPublic(true);
//    }
//
//}