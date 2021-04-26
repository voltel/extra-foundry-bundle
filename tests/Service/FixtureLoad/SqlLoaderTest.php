<?php


namespace Voltel\ExtraFoundryBundle\Tests\Service\FixtureLoad;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\SetUpFixtureEntityService;
use Voltel\ExtraFoundryBundle\Service\FixtureLoad\SqlDumpLoaderService;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Order;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\AddressFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CategoryFactory;
use Doctrine\ORM\EntityManager;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CustomerFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderItemFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\CustomerStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Story\ProductStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SqlLoaderTest extends KernelTestCase
{
    use ResetDatabase, Factories;


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

    public function testLoadSqlDump()
    {
        /** @var SqlDumpLoaderService $sqlLoaderService */
        $sqlLoaderService = self::$container->get('test.voltel_extra_foundry.sql_loader');
        $sqlLoaderService->loadSqlDump();

        AddressFactory::repository()->assert()->countGreaterThanOrEqual(CustomerStory::COUNT_CUSTOMER);
        CategoryFactory::repository()->assert()->countGreaterThan(0);
        CustomerFactory::repository()->assert()->count(CustomerStory::COUNT_CUSTOMER);
        OrderFactory::repository()->assert()->countGreaterThanOrEqual(2 * CustomerStory::COUNT_CUSTOMER);
        OrderItemFactory::repository()->assert()->countGreaterThanOrEqual(3 * CustomerStory::COUNT_CUSTOMER);
        ProductFactory::repository()->assert()->countGreaterThanOrEqual(ProductStory::COUNT_GENERIC_PRODUCTS);
    }//end of function

}