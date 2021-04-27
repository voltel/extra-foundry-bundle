<?php


namespace Voltel\ExtraFoundryBundle\Tests\Service\FixtureEntity;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Order;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Product;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CategoryFactory;
use Doctrine\ORM\EntityManager;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CustomerFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderItemFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\CustomerStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\ProductStory;
use Zenstruck\Foundry\AnonymousFactory;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\Instantiator;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class EntityProxyPersistServiceTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    const ATTRS = 'attributes';
    const STATES = 'states';

    public const SETUP_PRODUCTS = [
        'Mercedes Benz 1960' => [self::STATES => ['luxury', 'car', 'vintage', 'promoted'], self::ATTRS => ['productName' => 'Mercedes Benz 1960', 'registeredAt' => '2000-05-15'] ],
        'Rolls Royce 1920' => [self::STATES => ['luxury', 'car', 'vintage', 'unpromoted'], self::ATTRS => ['productName' => 'Rolls Royce 1920', 'registeredAt' => '2000-03-23'] ],
        'Ford Mondeo 2015' => [self::STATES => ['car', 'recent', 'unpromoted'], self::ATTRS => ['productName' => 'Ford Mondeo 2015'] ],
        'Tesla EV3' => [self::STATES => ['car', 'recent', 'promoted'], self::ATTRS => ['productName' => 'Tesla EV3'] ],
    ];



    /** @var EntityManager */
    private $em;


    protected function setUp(): void
    {
        // We basically want to initialize our bundle into an application (from HttpKernel component),
        // and check that the container has that service.
        $kernel = self::bootKernel();

        //$kernel = new VoltelExtraFoundryTestingKernel();
        //$kernel->boot();
        //$container = $kernel->getContainer();

        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->em->close();
        $this->em = null;
    }

    public function testPersistServiceExistsInContainer()
    {
        // Assertions
        $persistService = self::$container->get('test.voltel_extra_foundry.persist_service');
        $this->assertInstanceOf(EntityProxyPersistService::class, $persistService);
    }//end of function


    public function testPersistServicePersistFunction()
    {
        // Assertions
        /** @var EntityProxyPersistService $persistService */
        $persistService = self::$container->get('test.voltel_extra_foundry.persist_service');

        $factory = CategoryFactory::new()->withoutPersisting();

        foreach (Category::CATEGORIES as $c_this_category_name) {
            $persistService->createOne($factory, ['categoryName' => $c_this_category_name]);
        }//endforeach

        $this->assertEquals(count(Category::CATEGORIES), $persistService->getProxyCountForClass(Category::class));
        $this->assertTrue($persistService->isFlushRequired(), 'Failure to confirm that new entities require flush.');

        $persistService->persistAndFlushAll();

        $categories = $this->em->getRepository(Category::class)
            ->findBy(['categoryName' => Category::CATEGORIES[0]]);

        $this->assertNotNull($categories);
        $this->assertEquals(1, count($categories));

    }//end of function


    public function testStories()
    {
        $this->loadProductStory();
        // There are two algorithms creating the same number of generic products
        // Plus 20 Luxury cars
        $n_total_products = ProductStory::COUNT_GENERIC_PRODUCTS * 2 + ProductStory::COUNT_LUXURY_CARS;
        ProductFactory::repository()->assert()->count($n_total_products);

        $this->loadCustomerStory();
        // There are two algorithms creating the same number of customers
        CustomerFactory::repository()->assert()->count(CustomerStory::COUNT_CUSTOMER * 2);

        $this->loadOrderStory();
        $a_random_customer_proxy_batch = CustomerFactory::repository()->randomSet(5);
        $repo_order = OrderFactory::repository();
        $repo_order_item = OrderItemFactory::repository();
        //
        foreach ($a_random_customer_proxy_batch as $o_this_customer_proxy) {
            $repo_order->assert()->exists(['customer' => $o_this_customer_proxy]);
            $o_this_customer_random_order = $repo_order->random(['customer' => $o_this_customer_proxy]);
            /** @var Order $this_order_entity */
            $this_order_entity = $o_this_customer_random_order->object();

            $this_order_item_batch = $repo_order_item->findBy(['order' => $this_order_entity]);
            $this->assertNotNull($this_order_item_batch);
            $this->assertGreaterThanOrEqual(1, count($this_order_item_batch));
        }//endforeach
    }//end of function


    private function loadProductStory()
    {
        $productStory = self::$container->get('test.product_story');
        $productStory->build();
    }


    private function loadCustomerStory()
    {
        $customerStory = self::$container->get('test.customer_story');
        $customerStory->build();
    }


    private function loadOrderStory()
    {
        $orderStory = self::$container->get('test.order_story');
        $orderStory->build();
    }


    public function testFlashAllMethodForAfterPersistFunctionWithDedicatedFactory()
    {
        // Arrange
        /** @var EntityProxyPersistService $persistService */
        $persistService = self::$container->get('test.voltel_extra_foundry.persist_service');
        $productStory = self::$container->get('test.product_story');
        $productStory->build();
        $persistService->persistAndFlushAll();

        // Assert that each Product has now a "slug" which is set in the "afterPersist" callback
        $proxy_batch = ProductFactory::repository()->findAll();
        foreach ($proxy_batch as $this_proxy) {
            /** @var Product $this_product */
            $this_product = $this_proxy->object();

            $this->assertNotNull($this_product->getSlug());
        }//endif

    }//end of function


    public function testFlashAllMethodForAfterPersistFunctionWithAnonymousFactory()
    {
        // Arrange
        /** @var EntityProxyPersistService $persistService */
        $persistService = self::$container->get('test.voltel_extra_foundry.persist_service');
        $slugProvider = self::$container->get('voltel.product_slug_provider');
        $faker = self::$container->get('test.voltel_extra_foundry.faker_us');

        // Anonymous factory
        $factory = (new AnonymousFactory(Product::class))->withoutPersisting()
            ->withAttributes(function() use ($faker) {
                return [
                    'productName' => $faker->words(3, true),
                    'inPromotion' => $faker->boolean(75),
                    'registeredAt' => \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 days ago', '-5 years ago')),
                ];
            })
            ->instantiateWith((new Instantiator())->alwaysForceProperties(['registeredAt']))
            ->afterPersist(function (Product $product) use ($slugProvider) {
                $c_slug = $slugProvider->getSlugForProduct($product);
                $product->setSlug($c_slug);
            })
        ;

        // Note: this algorithm of test is not possible,
        //  since "addProxyBatch" and "addProxy" methods of persist service are now "protected"
        // Make it "hard" - register proxies w/o providing a factory
        //$proxy_batch = $factory->many(3)->create();
        //$persistService->addProxyBatch($proxy_batch);

        // Instead, provide a factory and count
        $persistService->createMany($factory, 3);

        $persistService->persistAndFlushAll();

        // Assert that each Product has now a "slug" which is set in the "afterPersist" callback
        $proxy_batch = ProductFactory::repository()->findAll();
        foreach ($proxy_batch as $this_proxy) {
            /** @var Product $this_product */
            $this_product = $this_proxy->object();

            $this->assertNotNull($this_product->getSlug());
        }//endif

    }//end of function


    /**
     * @dataProvider productDataProvider
     */
    public function testPersistServiceWithDataProvider(array $a_product_data)
    {
        $a_attributes = $a_product_data[self::ATTRS];
        $a_states = $a_product_data[self::STATES];

        /** @var EntityProxyPersistService $persistService */
        $persistService = self::$container->get('test.voltel_extra_foundry.persist_service');

        $factory_product = ProductFactory::new()->withoutPersisting();

        /** @var Product $product */
        $product = $persistService->createOne($factory_product, $a_attributes, $a_states);

        $persistService->persistAndFlushAll();

        $repo = ProductFactory::repository();
        $repo->assert()->exists([
            'productName' => $product->getProductName(),
        ]);
    }//end of function


    public function productDataProvider()
    {
        foreach (self::SETUP_PRODUCTS as $c_label => $a_data) {
            yield $c_label => [$a_data];
        }
    }


}//end of class
