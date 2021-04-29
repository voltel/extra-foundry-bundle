<?php


namespace Voltel\ExtraFoundryBundle\Tests\Service\FixtureEntity;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\ProxyJar;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CategoryFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;

/**
 * Note: This is a unit test, but since the Factories have some services injected
 * we need to load the kernel
 */
class ProxyJarTest extends KernelTestCase
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



    public function testGetEntityFqcnWithFactory()
    {
        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxyJar = new ProxyJar($factory_category);

        $this->assertSame(CategoryFactory::getClassName(), $proxyJar->getEntityFqcn());
    }


    public function testAddProxy()
    {
        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxyJar = new ProxyJar($factory_category);

        $proxy = $factory_category->create();
        $proxyJar->addProxy($proxy);
        $this->assertCount(1, $proxyJar->getProxies());

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy = $factory_product->create();

        $this->expectException(\LogicException::class);
        $proxyJar->addProxy($proxy);
    }


    public function testAddProxyBatch()
    {
        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxyJar = new ProxyJar($factory_category);

        $proxy_batch = $factory_category->many(3)->create();
        $proxyJar->addProxyBatch($proxy_batch);
        $this->assertCount(3, $proxyJar->getProxies());

        // An attempt to add to the jar a proxy of the wrong class (created by some other factory)
        $factory_product = ProductFactory::new()->withoutPersisting();
        $proxy_batch = $factory_product->many(3)->create();

        $this->expectException(\LogicException::class);
        $proxyJar->addProxyBatch($proxy_batch);
    }


    public function testGetEntityFqcnWithoutFactory()
    {
        $proxyJar = new ProxyJar();

        $factory_category = CategoryFactory::new()->withoutPersisting();
        $proxy = $factory_category->create();

        $proxyJar->addProxy($proxy);
        $this->assertSame(CategoryFactory::getClassName(), $proxyJar->getEntityFqcn());

        $proxy_batch = $factory_category->many(3)->create();
        $proxyJar->addProxyBatch($proxy_batch);
        $this->assertSame(CategoryFactory::getClassName(), $proxyJar->getEntityFqcn());
    }


}