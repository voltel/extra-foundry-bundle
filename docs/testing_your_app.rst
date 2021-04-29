.. include:: common_parts.rst

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    Creating entities for tests using VoltelExtraFoundryBundle services
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


1. One of the ways to set up your testing environment 
   with ``zenstruck/foundry`` bundle is to use `Global State`_. 
   If you want some initial database state 
   to be used for all tests in the test suite, 
   follow instructions in :ref:`test_app_with_global_state` section below. 
    
2. You can also create only those entities 
   that are needed to run a specific test.
   Read in :ref:`test_app_with_arranged_entities` section 
   on how you can do it a little easier with |bundle|. 

Those two approaches listed above are not mutually exclusive. 
You can have some `Global State`_ database entities created for all tests,
and for tests that need some additional entities,
you can create those at the start of the test function
in the "arrange" phase.
Learn about how you can use ``zenstruck/foundry`` for |AAA|. 

|bundle| can help to do either of the two things
with just a couple lines of code.

.. _test_app_with_global_state:

How to use Global State in your app tests
=========================================================================

The `Global State`_ approach saves time by eliminating the need 
to seed your database with the same initial data before every test. 
You can load ``zenstruck/foundry`` stories
that will create the initial database state,
using factories and even other stories.
This reduces time needed to run tests in your test suite.

The initial setup of the `Global State`_
in :file:`tests/bootstrap.php` of your application can look like this:
    
   .. code-block:: php
       
       // in tests/bootstrap.php
       
       //...  
    
       Zenstruck\Foundry\Test\TestState::addGlobalState(function () {
           // place all initial state loading logic in one specialized class
           \App\DataFake\Foundry\Story\GlobalStory::load();

           // or just load several stories one by one, similar to fixtures
           \App\DataFake\Foundry\Story\UserStory::load();
           \App\DataFake\Foundry\Story\ProductStory::load();
           \App\DataFake\Foundry\Story\OrderStory::load();
       });


But you can save even more time while running a test suite
by loading the initial state from a MySQL dump file (produced in advance)
instead of creating and persisting entities
with factories and stories,
even if it's only done once for all tests in a test suite.

Instead of creating entities "on the fly", 
we run a set of MySQL ``INSERT`` commands from the dump file. 

To do this, follows these steps:

    #. Using fixtures, :ref:`seed your development database <test_setup_step_one>` (in "dev" environment)
       with sample data you'd like to use as a `Global State`_ for your tests.

       Use ``zenstruck/foundry`` stories as described in :ref:`seeding_database`.

    #. :ref:`Export your data into a MySQL dump file <test_setup_step_two>`.

    #. :ref:`Place your dump file <test_setup_step_three>` inside your project
       (e.g. in :file:`var/mysql_dumps` directory).

    #. :ref:`Configure <test_setup_step_four>` your |bundle| to locate the dump file
       with SQL queries to re-create the initial database state in test environment.

    #. Create the :class:`GlobalStory` class with 
       :method:`GlobalStory::load()` method 
       to :ref:`load/import MySQL dump file <test_setup_step_five>` into a test database
       before the test suite is run.

Preparation
----------------------------------------------------------------------

Set up a new MySQL test database. In MySQL, configure the test user (probably the same as for your ``dev`` environment database) with appropriate schema privileges. Then, create the schema. 

#. Configure ``DATABASE_URL`` e.g. in :file:`.env.test` or :file:`.env.test.local`:

   .. code-block:: yaml
         
      # in ".env.test.local"

      DATABASE_URL=mysql://my_username:my_password@127.0.0.1:3306/my_database_test?serverVersion=5.7
            

#. Create the schema/database for ``test`` environment. 
   For example, you can do it from your application,  
   with the console command:

   .. code-block:: doscon

      > php bin/console doctrine:schema:create --env=test




.. _test_setup_step_one:

Step 1: Seed the development database
----------------------------------------------------------------------

.. code-block:: php

    // in UserFixture.php

    use App\DataFake\Foundry\Story\UserStory;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    
    class UserFixture extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            UserStory::load();
        }
    }

In this example, :method:`UserStory::build()` method will contain all the logic 
to create :entity:`User` entity and other related entities 
(e.g. :entity:`UserGroup` or :entity:`Address`, etc.). 

Conveniently, :class:`UserStory` might optionally use 
|SetUpFixtureEntityService| to create separate entities or 
|EntityProxyPersistService| to facilitate their persistence into the database, 
or use ``zenstruck/foundry`` `Model Factories`_ straightforward 
to create entities and persist/flush them into the database. 

Then, when fixture classes are ready, load fixtures as usual (see `Loading Fixtures`_ section of Symfony docs). Run in the terminal:

.. code-block:: doscon

    > php bin/console doctrine:fixtures:load



.. _test_setup_step_two:

Step 2: Dump your MySQL dev database into a file
----------------------------------------------------------------------

I find it easy to export MySQL data with MySQL Workbench graphic user interface, but there are definitely other ways to do it (e.g. with ``mysqldump`` terminal command or ``phpmyadmin`` export). See examples of ``mysqldump`` usage at `mysqldump program examples`_.

.. code-block:: doscon

   > mysqldump --user=my_username --password  my_database_dev > my_test_dump.sql


.. important:: Make sure that MySQL dump should contain ``DROP TABLE`` queries
   along with ``CREATE TABLE`` queries: 
   existing data and indexes might prevent inserting new records
   and therefore need to be taken out of the way. 



.. _test_setup_step_three:

Step 3: Place the dump file inside your project
----------------------------------------------------------------------
You can place your exported dump file anywhere in your project, since the location of the dump file should be configured (see next step). I place it in ``var/mysql_dumps`` folder.  


.. code-block:: text

    your_project/
    └─ var/
       ├─ mysql_dumps/
       ├─ cache/
       └─ log/



.. _test_setup_step_four:

Step 4: Configure |bundle|
----------------------------------------------------------------------
In your project create a new configuration file, 
e.g. :file:`voltel_extra_foundry.yaml` in ``config/packages/test`` directory. 

.. code-block:: text

    your_project/
    └─ config/
       └─ packages/
          └─ test/ 

An example of configuration is provided here:

.. code-block:: yaml

    voltel_extra_foundry:
        # Database (persistence layer) type: "mysql" is currently the only supported option.
        database_type:        mysql
    
        # Filesystem path of the directory where database dump files are located.
        dump_directory_path:  '%kernel.project_dir%/var/mysql_dumps'
    
        # File name (w/o file path) of database dump file that will be loaded in the current database.
        dump_file_name:       my_test_dump.sql
    
        # Doctrine connection name to use for data loading.
        connection_name:      default
    


.. _test_setup_step_five:

Step 5. Create the :class:`GlobalStory` class 
----------------------------------------------------------------------

.. code-block:: php

    // in GlobalStory.php
    
    namespace App\DataFake\Foundry\Story;
    
    
    use Voltel\ExtraFoundryBundle\Service\FixtureLoad\SqlDumpLoaderService;
    use Zenstruck\Foundry\Story;
    
    class GlobalStory extends Story
    {
        private $sqlDumpLoaderService;
    

        public function __construct(
            SqlDumpLoaderService $sqlDumpLoaderService
        )
        {
            $this->sqlDumpLoaderService = $sqlDumpLoaderService;
        }
    

        public function build(): void
        {
            $this->sqlDumpLoaderService->loadSqlDump();
        }
    
    }


|SqlDumpLoaderService| service will do two things:

    #. Using the ``database_type`` bundle configuration option, 
       it will locate an appropriate service implementing 
       |LoadDumpFromDatabaseInterface|. 

       Currently, there is only one service implementing this interface
       responsible for loading MySQL dumps: |MySqlDumpFileLoadingService|.

    #. The :method:`LoadDupmFromDatabaseInterface::loadSqlDump()` method
       will do the following:
    
       * Check the presence of file configured in ``dump_directory_path``
         and ``dump_file_name`` options;
       * Execute every SQL query in the dump file 
         skipping only empty strings
         and strings starting with ``--`` (comments). 
    
         .. note:: Name of the Doctrine provided PDO connection can be 
            configured in ``connection_name`` bundle configuration option
            and has a value of ``default``.  
        
         .. note:: The SQL queries being executed are one of the following:
        
             * ``DROP TABLE IF EXISTS``;
             * ``CREATE TABLE``;
             * ``LOCK TABLES``;
             * ``INSERT INTO``;
             * ``UNLOCK TABLES``;
             * Numerous auxiliary queries that look e.g. like:
 
               ``/*!40101 SET NAMES utf8 */;``
        
As a result, whenever you run your test suite, a `Global State`_ will load configured SQL dump in your test database. 


.. _test_app_with_arranged_entities:

How to set up custom entities in a test
=========================================================================
In the "arrange" phase of a functional test, 
you will sometimes rely on some specific entities 
existing in the database. 
Moreover, these entities might be different 
for the same test with each new dataset 
returned by the data provider.

|bundle| has a convenience service, |SetUpFixtureEntityService|,
with the :method:`createEntities()` method
to create batches of entities "on the fly" 
using an array with instructions as a *blueprint*.

The method will take a model factory, 
an array with instructions for entity "spawning", 
and an optional flag whether to immediately persist 
newly created entities or leave this task to the caller.

"Explicit" syntax of spawning instructions
----------------------------------------------------------------------

The "spawning" instructions are provided as an array of arrays (a chunk),
where keys of the outer array can either be omitted
or used as descriptive labels (e.g. for documentation purposes),
and values are nested arrays with three optional keys:

.. code-block:: php

    // How many entities to create in this spawn.
    // An integer. If "0", the instruction entrance will be skipped.
    'count' => 5


    // What states the factory should be modified with. 
    // The states are method names on the entity factory class.
    // If states take no arguments, just list their names.
    'states' => ['stateOne', 'stateTwo']

    // If a state takes arguments, pass the state name as a key
    // and an array of parameters as a value.
    'states' => ['stateOne' => ['param_1', 'param_2'], 'stateTwo' => ['param_1']]

    // If a state takes exactly one argument, the value can be passed w/o an array.
    // The following two instructions are equivalent:
    'states' => ['stateOne' => ['param_1'], 'stateTwo' => [5]]
    'states' => ['stateOne' => 'param_1', 'stateTwo' => 5]


    // Attributes with which the entities should be created.
    // These attributes will override the "defaults" provided by the entity factory.
    // The attribute values can be of any type, but most often they are scalar.
    'attributes' => ['attributeOne' => 15, 'attributeTwo' => 'some string']


As usual, it is easier to see the usage with an example:

.. code-block:: php

    use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
    use Zenstruck\Foundry\Test\Factories;
    use Zenstruck\Foundry\Test\ResetDatabase;
    use Voltel\ExtraFoundryBundle\Service\FixtureEntity\SetUpFixtureEntityService;

    class MyTest extends KernelTestCase
    {
        use ResetDatabase, Factories;

        private const SETUP_CUSTOMERS = [
            'customer_1' =>['states' => ['american', 'human']],
            'customer_2' =>['states' => ['ukrainian', 'human'],
            'customer_3' =>['states' => ['human'], 'attributes' => ['firstName' => 'John', 'lastName' => 'Doe']],
            'customer_4' =>['states' => ['human'], 'attributes' => ['firstName' => 'Богдан', 'lastName' => 'Мірошник']],
        ];

        public function __construct(SetUpFixtureEntityService $entityService)
        {
            $this->setUpFixtureEntityService = $entityService;
        }

        protected function setUp(): void
        {
            $kernel = self::bootKernel();
            // ...
        }
    
        public function testCreateEntities()
        {
            $factory_customer = CustomerFactory::new();
    
            $this->setUpFixtureEntityService-> createEntities($factory_customer, self::SETUP_CUSTOMERS, true);
        }
    }


The factory that is provided in the first argument
of the :method:`createEntities()` method
will be used as a stub which will be modified
if "spawning" instructions have any ``states`` listed.

In the second argument you should provide an array of arrays (a chunk)
with spawning instructions.
Bare this in mind if you use data providers,
since a chunk can include only one item (one nested array),
which may look a bit confusing.

The last argument to :method:`createEntities()`, if set to ``true``,
will signal to persist and flush all the entities
that |EntityProxyPersistService| has in its jars.
This is important if your tests use repository assertions
that expect certain entities to exist at some point in time.


Simplified ("implicit") syntax of spawning instructions
----------------------------------------------------------------------

If you create entities in "one go" (i.e. all instructions are provided at once),
as in the example above where :method:`createEntities()`
is passed a chunk of spawning instructions at once in the second parameter,
you might take advantage of a simplified instructions syntax.

The "implicit" (i.e. simplified) syntax,
as opposed to "explicit" syntax described above,
doesn't rely on reserved array keys (i.e. ``"count"``, ``"states"`` or ``"attributes"``)
but is resolved based on the following logic:

* if the first array item has key "0" and its value is numeric,
  the numeric value will be interpreted
  as equivalent to the "count" key with the "explicit" syntax;

* if a method exists on an entity factory with the name
  from an array item **value** (when the key in numeric),
  it will be interpreted as the name of the factory state to apply;

* if a method exists on an entity factory with the name
  from an array item **key** (when the key in not numeric),
  it will be interpreted as name of the factory state to apply.
  In this case, this array item's value will be interpreted as
  value(s) for this factory state.

* all other key-value pairs will be interpreted
  as attributes for the factory :method:`create()` method.

.. important:: With simplified syntax,
   do not use any custom array keys/values that cannot be
   interpreted as valid factory states or entity attributes.
   All unknown states or non-existent attributes will cause an error.

.. note:: If you want to have some custom data on the dataset array,
   e.g. for a test data provider, use "explicit" syntax
   where you can add custom keys.

With "explicit" syntax, you can add any custom keys
that are not reserved (i.e. "count", "states" or "attributes" keys)
to an array with spawning instructions
in order to pass additional information to data providers.

See the example below: the first set of instructions
defines a custom key "expect_error"
and the last set defines a custom key "exception_class".
This can be used in tests along with data providers and methods like
:method:`expectException()`.

 .. code-block:: php

    [
        'instructions set 1' => [
            'count' => 5,
            'states' => ['withStaffCount' => 3],
            'expect_error' => false
        ],
        'instructions set 2' => [
            'count' => 5,
            'states' => ['withStaffCount'],
            'expect_error' => true
        ],
        'instructions set 3' => [
            'count' => 5,
            'states' => ['withStaffCount' => 'many'],
            'exception_class' => BadMethodCallException::class
        ],
    ]


Using ``SetUpFixtureEntityService`` with data providers
----------------------------------------------------------------------

The same entity setup instructions
that are used to create batches of entities
can be used by data providers as data sets for test methods.

With PhpUnit `Data Providers`_ specification in mind,
remember that :method:`createEntities()` method takes
a chunk (an array of arrays) of instructions as the second parameter,
whether it has one set of instructions or more:

.. code-block:: php

    class MyOtherTest extends KernelTestCase
    {
        public const SETUP_PRODUCTS = [
            'product_1' => ['count' => 10, 'states' => ['luxury', 'car'] ] ,
            'product_2' => ['count' => 10, 'states' => ['ordinary', 'car'] ] ,
            'product_3' => ['count' => 20, 'states' => ['jewelry'] ],
            'product_4' => ['count' => 20, 'states' => ['furniture'] ],
            'product_5' => ['count' => 10, 'states' => ['house'] ],
            'product_6' => ['count' => 10, 'states' => ['luxury', 'apartment'] ],
            'product_7' => ['count' => 10, 'states' => ['ordinary', 'apartment'] ],
        ];

        //...

        /**
         * Asserts that count of created entities is as expected.
         *
         * @dataProvider productDataProviderOneSpawnInAChunk
         */
        public function testCreateEntitiesWithDataProvider(
            array $a_spawn_instructions, 
            int $n_expected_entity_count
        ) 
        {
            // ...
            $factory_product = ProductFactory::new(); 
            $setUpFixtureEntityService->createEntities($factory_product, $a_spawn_instructions, true);
    
            $repo = ProductFactory::repository();
            $repo->assertCount($n_expected_entity_count);
        }
    

        public function productDataProviderOneSpawnInAChunk()
        {
            foreach (self::SETUP_PRODUCTS as $c_label => $a_instructions_for_one_spawn) {

                $n_expected_entity_count = (int) $a_instructions_for_one_spawn['count'] ?? 1;

                // PhpUnit expects an array of arguments, so yield an array item
                yield $c_label => [
                    // argument one - an array of arrays (a chunk) that holds instructions
                    // for one "spawn" in this case
                    [$a_instructions_for_one_spawn], 

                    // argument two - an integer with expected entity count
                    $n_expected_entity_count
                ];
            }
        }

With "explicit" style of "spawning" instructions,
you can configure the expected outcome of the test
(i.e. whether to expect error/exception or not)
and even specify the class name of the expected exception.
For this, you need to add some logic both to the data provider
and the test method itself:

.. code-block:: php

    // in MyTest class

    private const STATES = SetUpFixtureEntityService::KEY_STATES;
    private const ATTRS = SetUpFixtureEntityService::KEY_ATTRIBUTES;
    private const COUNT = SetUpFixtureEntityService::KEY_COUNT;

    // custom keys
    private const ERROR = 'expect_error';
    private const EXCEPTION = 'exception_fqcn';

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


    /**
     * @dataProvider customerDataProviderForExplicitSetUpDefinition
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
        $repo->assertCount($n_expected_entity_count);

        // ...
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


To see more detailed examples of the ``SetUpFixtureEntityService`` use
in the testing environment, look in |SetUpFixtureEntityServiceTest|
class source code.


----------------------------------------------------------------------------

On the whole, the ``arrange`` phase of your tests may look neat
with only a couple of lines of code,
when all the instructions for creation (spawning) of tested entities
are given elsewhere (e.g., in class constants or data providers).



.. Links
.. _`Global State`: https://github.com/zenstruck/foundry/tree/v1.7.0#global-state

.. _`Model Factories`: https://github.com/zenstruck/foundry/tree/v1.7.0#model-factories

.. _`Loading Fixtures`: https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html#loading-fixtures

.. _`mysqldump program examples`: https://dev.mysql.com/doc/refman/5.7/en/mysqldump.html#mysqldump-option-examples

.. _`"Arrange", "Act", "Assert" testing patterns`: https://github.com/zenstruck/foundry/tree/v1.7.0#using-in-your-tests

.. _`Data Providers`: https://phpunit.readthedocs.io/en/9.5/writing-tests-for-phpunit.html?highlight=data%20provider#data-providers

.. Replace

.. |bundle| replace:: `VoltelExtraFoundryBundle`

.. |SetUpFixtureEntityService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\SetUpFixtureEntityService`

.. |EntityProxyPersistService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService`

.. |SqlDumpLoaderService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureLoad\SqlDumpLoaderService`

.. |LoadDumpFromDatabaseInterface| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureLoad\LoadDumpFromDatabaseInterface`

.. |MySqlDumpFileLoadingService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureLoad\MySqlDumpFileLoadingService`

.. |AAA| replace:: `"Arrange", "Act", "Assert" testing patterns`_

.. |SetUpFixtureEntityServiceTest| replace:: :class:`Voltel\ExtraFoundryBundle\Tests\Service\FixtureEntity\SetUpFixtureEntityServiceTest`
             