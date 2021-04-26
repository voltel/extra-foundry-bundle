<?php


namespace Voltel\ExtraFoundryBundle\Tests\Service\FixtureEntity;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\SetUpFixtureEntityService;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Doctrine\ORM\EntityManager;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CustomerFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SetUpFixtureEntityServiceTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    private const STATES = SetUpFixtureEntityService::KEY_STATES;
    private const ATTRS = SetUpFixtureEntityService::KEY_ATTRIBUTES;
    private const COUNT = SetUpFixtureEntityService::KEY_COUNT;

    private const ERROR = 'expect_error';
    private const EXCEPTION = 'exception_fqcn';

    private const SETUP_CUSTOMERS = [
        'customer_1' => [self::STATES => ['american', 'human'], self::ATTRS => []],
        'customer_2' => [self::STATES => ['ukrainian', 'human'], self::ATTRS => []],
        'customer_3' => [self::STATES => ['russian', 'human'], self::ATTRS => []],
        'customer_4' => [self::STATES => ['russian', 'company'], self::ATTRS => []],
        'customer_5' => [self::STATES => ['ukrainian', 'company'], self::ATTRS => []],
        'customer_6' => [self::STATES => ['american', 'company'], self::ATTRS => []],
        'customer_7' => [self::STATES => ['human'], self::ATTRS => ['firstName' => 'John', 'lastName' => 'Doe']],
        'customer_8' => [self::STATES => ['human'], self::ATTRS => ['firstName' => 'Богдан', 'lastName' => 'Мірошник']],
        'customer_9' => [self::STATES => ['human'], self::ATTRS => ['firstName' => 'Нонна', 'lastName' => 'Шестакова']],
        'customer_10' => [self::STATES => ['company'], self::ATTRS => ['firstName' => 'Сибирские пельмени']],
        'customer_11' => [self::STATES => ['company'], self::ATTRS => ['firstName' => 'Полтавські ковбаси']],
        'customer_12' => [self::STATES => ['company'], self::ATTRS => ['firstName' => 'Texas Roadhouse']],
    ];


    // These data sets are expected to generate an exception.
    // Note: SetUpFixtureEntityService with explicit dataset mode
    //  ignores any keys that are not "count", "states" or "attributes".
    //  You can use them for your own purposes, e.g. to pass some extra information to data providers in tests.
    private const SETUP_CUSTOMERS_WITH_ERRORS = [
        '1. state w/o an obligatory parameter' => [self::ERROR => true, self::STATES => ['company', 'withStaffCount'], self::ATTRS => ['firstName' => 'Полтавські ковбаси']],
        '2. state w/o an obligatory parameter, exception FQCN specified' => [
            self::EXCEPTION => \ArgumentCountError::class,
            self::STATES => ['company', 'withStaffCount'], self::ATTRS => ['firstName' => 'Полтавські ковбаси']
        ],
        '3. unknown state' => [self::ERROR => true, self::STATES => ['company', 'unknownStateName'], self::ATTRS => ['firstName' => 'Полтавські ковбаси']],
        '4. unknown state, exception FQCN specified' => [
            self::EXCEPTION => \BadMethodCallException::class,
            self::STATES => ['company', 'unknownStateName'], self::ATTRS => ['firstName' => 'Полтавські ковбаси']
        ],
        '5. state w/ bad parameter' => [self::ERROR => true, self::STATES => ['company', 'withStaffCount' => 'this must be an integer'], self::ATTRS => ['firstName' => 'Texas Roadhouse']],
        '6. state w/ bad parameter, exception FQCN specified' => [
            self::EXCEPTION => \TypeError::class,
            self::STATES => ['company', 'withStaffCount' => 'this must be an integer'],
        ],
        '7. state w/ unknown attribute' => [self::ERROR => true, self::STATES => ['company', 'withStaffCount' => 5], self::ATTRS => ['unknownAttribute' => 'Texas Roadhouse']],
        '8. state w/ unknown attribute, exception FQCN specified' => [
            self::EXCEPTION => \InvalidArgumentException::class,
            self::STATES => ['company'], self::ATTRS => ['unknownAttribute' => 'Texas Roadhouse'],
        ],
    ];


    // Word "error" or "ERROR" in the label will be a signal to the data provider
    // that this dataset is expected to generate an error/exception
    private const SETUP_CUSTOMERS_SIMPLIFIED = [
        '1. two companies, state with one parameter as scalar' => [2, 'company', 'withStaffCount' => 5, 'firstName' => 'Сибирские пельмени'],
        '2. one company, state with one parameter as array' => ['company', 'withStaffCount' => [5], 'firstName' => 'Сибирские пельмени'],
        '3. one company, state with parameters' => ['company', 'withStaffCount' => [3, 10], 'firstName' => 'Полтавські ковбаси'],
        // Method for state "company" will receive a parameter, int (5), but it will be ignored (as extra parameters during invocation are ignored in PHP).
        '4. three companies with state with parameters' => [3, 'company' => 5, 'withStaffCount' => [3, 10], 'firstName' => 'Полтавські ковбаси'],
        // This should generate an exception, since state "withStaffCount" must receive at least one parameter
        '5. expect ERROR - state "withStaffCount" without an obligatory parameter' => ['company', 'withStaffCount', 'firstName' => 'Texas Roadhouse'],
        '6. expect ERROR - state "organization" does not exist' => ['organization', 'firstName' => 'Texas Roadhouse'],
        '7. expect ERROR - state/property "organization" does not exist' => ['company', 'organization' => true],
    ];

    public const SETUP_PRODUCTS = [
        // E.g. spawning instructions in "product_1" dataset will create 10 Product entities
        // and add related Category entities to "categoryCollection"
        // as defined by the "luxury" state and "car" state.
        '1. Ten luxury cars'         => [self::COUNT => 10, self::STATES => ['car', 'luxury', ] ] ,
        '2. Ten ordinary cars'       => [self::COUNT => 10, self::STATES => ['ordinary', 'car'] ] ,
        '3. Twenty jewelry pieces'   => [self::COUNT => 20, self::STATES => ['jewelry'] ], // "jewelry" state adds "luxury" category
        '4. Twenty furniture items'  => [self::COUNT => 20, self::STATES => ['furniture'] ],
        '5. Ten houses'              => [self::COUNT => 10, self::STATES => ['house'] ],
        '6. Ten luxury apartments'   => [self::COUNT => 10, self::STATES => ['luxury', 'apartment'] ],
        '7. Ten ordinary apartments' => [self::COUNT => 10, self::STATES => ['ordinary', 'apartment'] ],
    ];


    public const SETUP_PRODUCTS_SIMPLIFIED = [
        // This spawning instructions are equivalent to instructions in self::SETUP_PRODUCTS
        '1. Ten luxury cars'         => [10, 'luxury', 'car'] ,
        '2. Ten ordinary cars'       => [10, 'ordinary', 'car'] ,
        '3. Twenty jewelry pieces'   => [20, 'jewelry'], // "jewelry" state adds "luxury" category
        '4. Twenty furniture items'  => [20, 'furniture'],
        '5. Ten houses'              => [10, 'house'],
        '6. Ten luxury apartments'   => [10, 'luxury', 'apartment'],
        '7. Ten ordinary apartments' => [10, 'ordinary', 'apartment'],
    ];


    private const EXPECTED_PRODUCT_COUNT = [
        Category::CARS => 20, // 10 luxury cars + 10 ordinary cars
        Category::JEWELRY => 20,
        Category::FURNITURE => 20,
        Category::HOUSES => 10,
        Category::APARTMENTS => 20, // 10 luxury apartments + 10 ordinary apartments
        Category::LUXURY => 40, // 10 luxury cars + 20 jewelery (adds "luxury" category) + 10 luxury apartments,
        Category::ORDINARY => 20, // 10 ordinary cars + 10 ordinary apartments
    ];



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

    /**
     * Creates Customer entities "in one go".
     * Asserts some count of entities of certain type is as expected.
     */
    public function testCreateEntitiesWithSpawningInstructions()
    {
        /** @var SetUpFixtureEntityService $setUpFixtureEntityService */
        $setUpFixtureEntityService = self::$container->get('test.voltel_extra_foundry.entity_setup');

        $factory_customer = CustomerFactory::new()->withoutPersisting();
        $setUpFixtureEntityService->createEntities($factory_customer, self::SETUP_CUSTOMERS, true);

        $repo = CustomerFactory::repository();
        $n_expected_customers = count(self::SETUP_CUSTOMERS);
        $repo->assert()->count($n_expected_customers);

        $a_humans = $repo->findBy(['isOrganization' => false]);
        $this->assertNotNull($a_humans);
        $n_expected_humans = $n_expected_customers/2;
        $this->assertCount($n_expected_humans, $a_humans);

        $a_companies = $repo->findBy(['isOrganization' => true]);
        $this->assertNotNull($a_companies);
        $n_expected_companies = $n_expected_customers - $n_expected_humans;
        $this->assertCount($n_expected_companies, $a_companies);

        $repo->assert()->exists(['firstName' => 'John', 'lastName' => 'Doe']);
    }//end of function



    /**
     * Creates all products "in one go", then asserts that categories enforced by ProductFactory states
     * were created correctly
     * @dataProvider getExpectedCategoryCount
     */
    public function testCreateEntitiesWithRelatedCollectionEntities(string $c_category_name, int $n_expected_count)
    {
        // "Arrange" phase
        /** @var SetUpFixtureEntityService $setUpFixtureEntityService */
        $setUpFixtureEntityService = self::$container->get('test.voltel_extra_foundry.entity_setup');

        $factory_product = ProductFactory::new()->withoutPersisting();
        $setUpFixtureEntityService->createEntities($factory_product, self::SETUP_PRODUCTS, true);


        // "Assert" phase
        // Total number of product items for each Category should match
        $qb = ProductFactory::repository()->createQueryBuilder('product')
            ->select('COUNT(product)')
            ->leftJoin('product.categoryCollection', 'category')
            ->andWhere('category.categoryName = :category_name')
            ->setParameter('category_name', $c_category_name)
            ;
        $n_products_count = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals($n_expected_count, $n_products_count);
    }//end of function


    /**
     * Creates one spawn of entities at a time with data providers.
     * First data provider gives instructions for just one spawn in a set.
     * Second data provider gives instructions for several spawns in a set (similar, to tests above).
     *
     * Asserts that count of created entities is as expected.
     *
     * @dataProvider productDataProviderOneSpawnInAChunk
     * @dataProvider productDataProviderOneSpawnInAChunkSimplified
     * @dataProvider productDataProviderSeveralSpawnsInAChunk
     */
    public function testCreateEntitiesWithDataProvider(
        array $a_chunk_of_spawn_instructions,
        int $n_expected_entity_count
    )
    {
        // "Arrange" phase
        /** @var SetUpFixtureEntityService $setUpFixtureEntityService */
        $setUpFixtureEntityService = self::$container->get('test.voltel_extra_foundry.entity_setup');

        $factory_product = ProductFactory::new(); // ->withoutPersisting();
        $setUpFixtureEntityService->createEntities($factory_product, $a_chunk_of_spawn_instructions, true);

        $repo = ProductFactory::repository();
        $repo->assert()->count($n_expected_entity_count);
    }//end of function


    /**
     * @dataProvider customerDataProviderForSimplifiedSetUpDefinition
     * @dataProvider customerDataProviderForExplicitSetUpDefinition
     * @dataProvider customerDataProviderForMixedSetUpDefinition
     */
    public function testCreateEntitiesWithParameterizedState(
        array $a_spawn_instructions,
        int $n_expected_entity_count,
        bool $l_expect_error = false,
        string $c_expect_exception_fqcn = null
    )
    {
        // "Arrange" phase
        /** @var SetUpFixtureEntityService $setUpFixtureEntityService */
        $setUpFixtureEntityService = self::$container->get('test.voltel_extra_foundry.entity_setup');

        $factory_customer = CustomerFactory::new(); // ->withoutPersisting();

        if ($l_expect_error) {
            $this->expectException($c_expect_exception_fqcn ?? \Throwable::class);
        }//endif

        // Try and create/persist a spawn of Customer entities with provided instructions
        $setUpFixtureEntityService->createEntities($factory_customer, [$a_spawn_instructions], true);

        $repo = CustomerFactory::repository();
        $repo->assert()->count($n_expected_entity_count);

        // Assert expected staffCount property value was set correctly by the parameterized factory state
        if (array_key_exists('withStaffCount', $a_spawn_instructions)) {
            if (is_array($a_spawn_instructions['withStaffCount'])) {
                $min_range_count = $a_spawn_instructions['withStaffCount'][0];
                $max_range_count = $a_spawn_instructions['withStaffCount'][1] ?? $a_spawn_instructions['withStaffCount'][0];
            } else {
                $min_range_count = $max_range_count = $a_spawn_instructions['withStaffCount'];
            }//endif

            foreach ($repo->findAll() as $this_customer_proxy) {
                /** @var Customer $this_customer */
                $this_customer = $this_customer_proxy->object();

                $this->assertGreaterThanOrEqual($min_range_count, $this_customer->getStaffCount());
                $this->assertLessThanOrEqual($max_range_count, $this_customer->getStaffCount());
            }//endforeach
        }//endif

    }//end of function


    public function customerDataProviderForSimplifiedSetUpDefinition()
    {
        foreach (self::SETUP_CUSTOMERS_SIMPLIFIED as $c_label => $a_one_spawn_instructions) {
            // This dataset should generate an exception if there is a word "error" in the label
            $l_expect_error = false !== stripos($c_label, 'error');

            // Count of entities to create can be found in: under key "0" (if the value is numeric)
            $n_expected_entity_count = $a_one_spawn_instructions[0] ?? null;
            $n_expected_entity_count = is_numeric($n_expected_entity_count) ? $n_expected_entity_count : 1;

            yield $c_label => [
                $a_one_spawn_instructions, $n_expected_entity_count, $l_expect_error
            ];
        }
    }


    public function customerDataProviderForExplicitSetUpDefinition()
    {
        foreach (self::SETUP_CUSTOMERS_WITH_ERRORS as $c_label => $a_one_spawn_instructions) {
            // This dataset should generate an exception
            // 1) if there is a custom key "expect_error" set to "true", or
            // 2) if there is a custom key "exception_fqcn" with FQCN of the expected exception,
            $c_expect_exception_fqcn = $a_one_spawn_instructions[self::EXCEPTION] ?? null;
            $l_expect_error = $a_one_spawn_instructions[self::ERROR] ?? is_null($c_expect_exception_fqcn);

            // Count of entities to create can be found in a special key "count"
            $n_expected_entity_count = $a_one_spawn_instructions[self::COUNT] ?? 1;

            yield $c_label => [
                $a_one_spawn_instructions, $n_expected_entity_count, $l_expect_error, $c_expect_exception_fqcn
            ];
        }
    }


    public function customerDataProviderForMixedSetUpDefinition()
    {
        // Combining "explicit" and "implicit" (i.e. simplified) spawning instructions,
        // just to make things "harder". Otherwise, there should be two separate data providers.
        $a_combined_customers = array_merge(self::SETUP_CUSTOMERS_SIMPLIFIED, self::SETUP_CUSTOMERS_WITH_ERRORS);

        foreach ($a_combined_customers as $c_label => $a_one_spawn_instructions) {
            // This dataset should generate an exception
            // 1) if there is a custom key "expect_error" set to "true", or
            // 2) if there is a custom key "exception_fqcn" with FQCN of the expected exception,
            // 3) if there is a word "error" in the label, or
            $c_expect_exception_fqcn = $a_one_spawn_instructions[self::EXCEPTION] ?? null;
            $l_expect_error = boolval($a_one_spawn_instructions[self::ERROR] ?? $c_expect_exception_fqcn ??
                false !== stripos($c_label, 'error'));

            // Count of entities to create can be found in:
            // 1) a special key "count", or
            // 2) under key "0" (if the value is numeric) for simplified instructions
            $n_expected_entity_count = $a_one_spawn_instructions[self::COUNT] ?? $a_one_spawn_instructions[0] ?? null;
            $n_expected_entity_count = is_numeric($n_expected_entity_count) ? (int) $n_expected_entity_count : 1;

            yield $c_label => [
                $a_one_spawn_instructions, $n_expected_entity_count, $l_expect_error, $c_expect_exception_fqcn
            ];
        }
    }

    public function productDataProviderOneSpawnInAChunk()
    {
        foreach (self::SETUP_PRODUCTS as $c_label => $a_one_spawn_instructions) {
            $n_expected_entity_count = $a_one_spawn_instructions['count'] ?? 1;
            $a_chunk_of_spawn_instructions = [$a_one_spawn_instructions];

            // first, PhpUnit expects an array of arguments.
            // second, Our first argument is an array of arrays
            yield $c_label => [
                $a_chunk_of_spawn_instructions, $n_expected_entity_count
            ];
        }
    }


    public function productDataProviderOneSpawnInAChunkSimplified()
    {
        foreach (self::SETUP_PRODUCTS_SIMPLIFIED as $c_label => $a_one_spawn_instructions) {
            $n_expected_entity_count = $a_one_spawn_instructions[0] ?? 1;
            $a_chunk_of_spawn_instructions = [$a_one_spawn_instructions];

            yield $c_label => [
                $a_chunk_of_spawn_instructions, $n_expected_entity_count
            ];
        }
    }

    /**
     * For each test, will return a chunk (an array of arrays)
     * with several spawning instructions for Product entities
     */
    public function productDataProviderSeveralSpawnsInAChunk()
    {
        $n_chunk_size = 2;
        $a_chunks = array_chunk(self::SETUP_PRODUCTS, $n_chunk_size, true);

        foreach ($a_chunks as $a_this_chunk) {
            $n_expected_entity_count = array_reduce($a_this_chunk, function($carry, $item) {
                return $carry + $item['count'] ?? 1;
            });

            $c_first_key = array_key_first ($a_this_chunk);
            $c_last_key = array_key_last($a_this_chunk);
            $c_label = $c_first_key . ' - ' . $c_last_key;

            yield $c_label => [
                $a_this_chunk, $n_expected_entity_count
            ];
        }
    }


    /**
     * data provider
     */
    public function getExpectedCategoryCount()
    {
        foreach (self::EXPECTED_PRODUCT_COUNT as $c_category_name => $n_expected_count) {
            yield $c_category_name => [
                $c_category_name, $n_expected_count
            ];
        }
    }

}