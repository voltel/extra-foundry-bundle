<?php


namespace Voltel\ExtraFoundryBundle\Tests\Service\FixtureEntity;

use Doctrine\ORM\EntityManager;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Product;
use Zenstruck\Foundry\AnonymousFactory;
use Zenstruck\Foundry\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\ProxyJar;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\ProxyJarCollection;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CategoryFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;

/**
 * Note: This is a unit test, but since the Factories have some services injected
 * we need to load the kernel
 */
class ProxyJarCollectionTest extends KernelTestCase
{
    /** @var EntityManager */
    private $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        // doing this is recommended to avoid memory leaks
        $this->em->close();
        $this->em = null;
    }


    public function testAddProxy()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy = $factory_category->create();
        $proxyJarCollection->addProxy($proxy, $factory_category);
        $this->assertCount(1, $proxyJarCollection->getProxyJars());

        // Add to the jar collection a proxy of some other class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy = $factory_product->create();
        $proxyJarCollection->addProxy($proxy, $factory_product);
        $this->assertCount(2, $proxyJarCollection->getProxyJars());

        // Add to the jar collection a proxy created by some anonymous factory
        // Note: we do not pass factory with the second argument
        $factory = (new AnonymousFactory(Category::class))->withoutPersisting();
        $proxy = $factory->create();
        $proxyJarCollection->addProxy($proxy);
        $this->assertCount(3, $proxyJarCollection->getProxyJars());

        // Note: we do not pass factory with the second argument
        // Since there is no factory passed, and the FQCN is the same,
        // the count of jars shouldn't change
        $factory = (new AnonymousFactory(Category::class))->withoutPersisting();
        $proxy = $factory->create();
        $proxyJarCollection->addProxy($proxy);
        $this->assertCount(3, $proxyJarCollection->getProxyJars());
    }


    public function testAddProxyBatch()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy_batch = $factory_category->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_category);
        $this->assertCount(1, $proxyJarCollection->getProxyJars());

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_product);
        $this->assertCount(2, $proxyJarCollection->getProxyJars());

        // Add to the jar collection a batch of proxies created by some anonymous factory
        // Note: we do not pass factory as a second argument
        $factory = (new AnonymousFactory(Category::class))->withoutPersisting();
        $proxy_batch = $factory->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch);
        $this->assertCount(3, $proxyJarCollection->getProxyJars());

        // Add to the jar collection a batch of proxies created by some anonymous factory
        // It is another factory object, but the same class of entities.
        // Since, factory is not passed as the second argument, the count of jars shouldn't change
        $factory = (new AnonymousFactory(Category::class))->withoutPersisting();
        $proxy_batch = $factory->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch);
        $this->assertCount(3, $proxyJarCollection->getProxyJars());
    }

    public function testClear()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy_batch = $factory_category->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch);
        $this->assertCount(1, $proxyJarCollection->getProxyJars());

        $proxyJarCollection->clear();
        $this->assertCount(0, $proxyJarCollection->getProxyJars());
    }

    public function testGetEntityFqcnArrayForAllEntityProxies()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy = $factory_category->create();
        $proxyJarCollection->addProxy($proxy, $factory_category);

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_product);

        $this->assertCount(2, $proxyJarCollection->getEntityFqcnArrayForAllEntityProxies());
    }


    public function testGetProxyBatchForEntityFqcn()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_product);

        $this->assertCount(3, $proxyJarCollection->getProxyBatchForEntityFqcn(Product::class));
    }


    public function testGetProxyBatchGroupedByFqcn()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy = $factory_category->create();
        $proxyJarCollection->addProxy($proxy, $factory_category);

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_product);

        $a_grouped_proxy_batch = $proxyJarCollection->getProxyBatchGroupedByFqcn();
        $this->assertCount(2, $a_grouped_proxy_batch);
        $this->assertSame(Category::class, array_keys($a_grouped_proxy_batch)[0]);
        $this->assertSame(Product::class, array_keys($a_grouped_proxy_batch)[1]);
    }


    public function testGetProxyCount()
    {
        $proxyJarCollection = new ProxyJarCollection();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy = $factory_category->create();
        $proxyJarCollection->addProxy($proxy, $factory_category);

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_product);

        $this->assertSame(1, $proxyJarCollection->getProxyCount(Category::class));
        $this->assertSame(3, $proxyJarCollection->getProxyCount(Product::class));
        $this->assertSame(4, $proxyJarCollection->getProxyCount());
    }


    public function testGetProxyJarBatchForEntityFqcn()
    {
        $proxyJarCollection = new ProxyJarCollection();

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory_product);

        // Add to the jar collection a batch of proxies created by some anonymous factory
        $factory = (new AnonymousFactory(Product::class))->withoutPersisting();
        $proxy_batch = $factory->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch, $factory);

        // Add to the jar collection a batch of proxies created by some anonymous factory
        // Note: we do not pass factory when adding proxies
        $factory = (new AnonymousFactory(Product::class))->withoutPersisting();
        $proxy_batch = $factory->many(3)->create();
        $proxyJarCollection->addProxyBatch($proxy_batch);

        $this->assertCount(3, $proxyJarCollection->getProxyJarBatchForEntityFqcn(Product::class));
    }

}