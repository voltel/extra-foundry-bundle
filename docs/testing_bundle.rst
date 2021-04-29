.. include:: common_parts.rst

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    Testing VoltelExtraFoundryBundle
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

If you want to see examples of using |bundle| and/or test it,
you should look into the :file:`tests` directory.

Two files in the root of the bundle are also important for testing set-up:

* :file:`phpunit.xml.dist`
* :file:`cli-config.php`



Bundle's :file:`tests` directory structure
======================================================================

.. code-block:: text

    your_project/
    └─ tests/
       ├─ Service/
       │  ├─ FixtureEntity/
       │  └─ FixtureLoad/
       ├─ Setup/
       │  ├─ Entity/
       │  ├─ Factory/
       │  ├─ Kernel/
       │  ├─ MySqlDump/
       │  ├─ Service/
       │  └─ Story/
       └─ bootstrap.php

* All tests are located in :file:`tests/Service` directory.

* In :file:`tests/Setup/Entity` directory, you will find
  entities that are going to be created during the tests.

* In :file:`tests/Setup/Factory` directory,
  you will find ``zendstruck/foundry`` model factories
  describing the related entities (one factory per entity).

* In :file:`tests/Setup/Story` directory,
  you shall definitely look at the way entities are created and persisted
  using services from |bundle|.

  .. note:: Inspect classes in :file:`tests/Setup/Story` directory
     to see examples of suggested |EntityProxyPersistService| usage.

* In :file:`tests/Setup/Service` directory, you will find a faux service
  that is used in ``afterPersist`` callback in :class:`ProductFactory` class.
  It is needed in order to to change the ``slug`` property
  on :entity:`Product` entity and assert during the test
  that the ``afterPersist`` callback was indeed invoked.

* The :file:`tests/Setup/Kernel` directory contains
  the only file with |VoltelExtraFoundryTestingKernel| class
  where all services used during the tests are configured,
  including the services provided by |bundle| and Doctrine.

* In :file:`tests/Setup/MySqlDump` directory,
  :file:`mysql_dump_for_tests.sql` file contains a MySQL dump
  that is asserted during the tests
  to be properly loaded/imported into the database.

* File :file:`bootstrap.php` was modified to retrieve
  the value of ``DATABASE_URL`` from :file:`phpunit.xml.dist`
  and set it in the super global ``$_ENV`` array
  to be later used when running Doctrine CLI commands (read below).

How to set up bundle tests
======================================================================

Overview
----------------------------------------------------------------------

To set up testing with PhpUnit using a MySQL test database,
several steps need to be done:

#. :ref:`Configure the kernel class <step_one_configure_kernel>` that is used by the testing suite;
#. :ref:`Create a test MySQL database <step_two_test_database>` (e.g. "voltel_extra_foundry_test");
#. :ref:`Set up MySQL schema <step_three_set_up_schema>`.


.. _step_one_configure_kernel:

Step 1: Configure the kernel class
----------------------------------------------------------------------

    The testing kernel is configured in |VoltelExtraFoundryTestingKernel| class.

    .. code-block:: php

        // in VoltelExtraFoundryTestingKernel.php

        class VoltelExtraFoundryTestingKernel extends Kernel
        {
            // ...

            public function registerContainerConfiguration(LoaderInterface $loader)
            {
                $loader->load(function (ContainerBuilder $container) use ($loader) {
                    // Services that are used in tests
                    // ...

                    // Configure Doctrine
                    $container->loadFromExtension('doctrine', [
                        'dbal' => [
                            'default_connection' => 'default',
                            'connections' => [
                                'default' => [
                                    'url' => $_ENV['DATABASE_URL'],
                                    'logging' => false,
                                    'override_url' => true,
                                    ]
                                ],
                            ],
                        'orm' => [/* ... */]
                    ]);
                });
            }

        }//end of class

    The connection URL in the code snippet above
    depends on the environmental variable `DATABASE_URL`
    which must be configured in :file:`phpunit.xml.dist`:

    .. code-block:: xml

        <!-- in phpunit.xml.dist -->

        <php>
           <!-- ... -->
            <env name="DATABASE_URL" value="mysql://testuser:password@127.0.0.1:3306/voltel_extra_foundry_test?serverVersion=5.7" />

            <env name="KERNEL_CLASS" value="Voltel\ExtraFoundryBundle\Tests\Setup\Kernel\VoltelExtraFoundryTestingKernel" />

        </php>

    .. important:: Change ``DATABASE_URL`` definition in :file:`phpunit.xml.dist`
       to reflect your MySQL test user's credentials
       and test database/schema name.

    .. note:: The configuration in :file:`phpunit.xml.dist`
       also contains a definition for another
       environmental variable, ``KERNEL_CLASS``,
       which is internally used by |KernelTestCase|.

.. _step_two_test_database:

Step 2: Create a test MySQL database
----------------------------------------------------------------------

In MySQL, create a new database, e.g. "voltel_extra_foundry_test". Configure your test user to have appropriate privileges for this database.

.. code-block:: doscon

    > mysql --user=root --password

    mysql> CREATE USER IF NOT EXISTS 'testuser'@'localhost' IDENTIFIED BY 'password';
    mysql> CREATE DATABASE IF NOT EXISTS voltel_extra_foundry_test;
    mysql> GRANT ALL ON voltel_extra_foundry_test.* TO 'testuser'@'localhost';
    mysql> quit;


.. note:: Database name, host URL, username, user password must match those
   configured in :file:`phpunit.xml.dist`
   for ``DATABASE_URL`` environmental variable (see above).


.. _step_three_set_up_schema:

Step 3: Set up MySQL schema
----------------------------------------------------------------------

The :file:`cli-config.php` in the root of the project is required for Doctrine bundle CLI tool. The file is quite short; it has only a few things to do:

* boot our custom kernel (|VoltelExtraFoundryTestingKernel|);
* retrieve entity manager from the kernel;
* return an instance of |HelperSet| for the provided entity manager.


With Doctrine CLI configured, run in the terminal:

.. code-block:: shell

   $ vendor/bin/doctrine orm:schema-tool:create

or, in Windows command prompt:

.. code-block:: doscon

    > "vendor/bin/doctrine" orm:schema-tool:create


Run bundle tests
======================================================================

Run the tests with this command:

.. code-block:: doscon

    > "vendor/bin/simple-phpunit"

.. note:: The following exact command was run under Windows
   to obtain the MySQL test database state and produce the dump
   that is located in :file:`tests/Setup/MySqlDump/mysql_dump_for_tests.sql`.

   .. code-block:: doscon

       > "vendor/bin/simple-phpunit" tests/Service/FixtureEntity/EntityProxyPersistServiceTest.php --filter=testStories






.. Links

.. _`KERNEL_CLASS_ADMONITION`: https://symfony.com/doc/current/testing.html#your-first-functional-test


.. Replace

.. |bundle| replace:: `VoltelExtraFoundryBundle`

.. |SetUpFixtureEntityService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\SetUpFixtureEntityService`

.. |EntityProxyPersistService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService`

.. |SqlDumpLoaderService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureLoad\SqlDumpLoaderService`

.. |LoadDumpFromDatabaseInterface| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureLoad\LoadDumpFromDatabaseInterface`

.. |MySqlDumpFileLoadingService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureLoad\MySqlDumpFileLoadingService`

.. |VoltelExtraFoundryTestingKernel| replace:: :class:`Voltel\ExtraFoundryBundle\Tests\Setup\Kernel\VoltelExtraFoundryTestingKernel`

.. |KernelTestCase| replace:: :class:`Symfony\Bundle\FrameworkBundle\Test\KernelTestCase`

.. |HelperSet| replace:: :class:`Symfony\Component\Console\Helper\HelperSet`



