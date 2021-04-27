.. include:: common_parts.rst

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    Seeding the development database with fixture entities
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

The recommended way to seed the database with sample data
while developing is to use `DoctrineFixturesBundle`_.
To install Doctrine Fixture bundle, follow the instructions
given on `DoctrineFixturesBundle`_ page of Symfony docs.

.. _seeding_database:

Steps to follow to seed the database
======================================================================

In general, the steps required to seed the development database with entities
aren't much different with |bundle| from when you use
vanilla ``zenstruck/foundry`` bundle.


The difference is mostly with the implementation of stories
(classes extending from |Story|).
|bundle| offers a |s_EntityProxyPersistService| (described :ref:`in this chapter <seed_dev_database>`)
and |s_SetUpFixtureEntityService| (described in ":ref:`test_app_with_arranged_entities`" section)
which make populating a database easy to code, clear to read and fast to execute.


#. First, `create your Doctrine entities <https://symfony.com/doc/current/doctrine.html#creating-an-entity-class>`_.

#. Then, for each entity, create a corresponding custom factory class
   extending |ModelFactory| as described in `Model Factories`_.

   For reasons :ref:`described elsewhere <states_for_arrays>`, it is recommended to extend
   |AbstractFactory| class which, in its turn, extends |ModelFactory| class.
   But it's not a big deal if you don't.

#. Use your custom factories inside your custom story classes
   as described in `Stories`_.

   For example, inside the :class:`CustomerStory` class,
   you can create a batch of customers (:entity:`Customer` entities)
   using :class:`CustomerFactory`
   and, for each customer, create one or several addresses
   (:entity:`Address` entities) using :class:`AddressFactory`.

   To get automatic access to the bundle's |EntityProxyPersistService|
   service, extend your story class from |AbstractStory| class.

   Otherwise, use usual Symfony's dependency injection tricks
   to get access to the persist service inside your story class
   extending the |Story| class
   (e.g. type-hint |EntityProxyPersistService|
   in the ``__construct`` method of your story class).

#. Use your custom stories inside Doctrine Fixture classes.

   So, your custom Fixture class extending from |Fixture|
   may look like this:

   .. code-block:: php

      class DataFixture extends Fixture
      {
          public function load(ObjectManager $manager)
          {
                ProductStory::load();
                // see example of CustomerStory class below
                CustomerStory::load();
                OrderStory::load();
          }
      }

   For other examples of `Using with DoctrineFixtureBundle`_
   read ``zenstruck/foundry`` docs.

.. _seed_dev_database:

Create entities with delayed persist/flush
======================================================================

Create **separate** entities with delayed persist/flush
----------------------------------------------------------------------

Use :method:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService::createOne()` method
to create a single entity.

Pass |Factory| object as the first parameter.
In the second optional parameter, you can pass either an array of attributes for the factory,
or a callback function to return an array of attributes
as described in `Attributes`_ section of ``zenstruck/foundry`` docs.

The last (third) parameter is optional as well.
It may accept an array with names of the factory states
(i.e. names of the factory class methods, as described in `Reusable Model Factory "States"`_).

.. note:: States in the final parameter can be presented as
   just state names for methods that do not require arguments,
   or an associative array elements where an array key is the state/method name
   and an array value is the method argument(s).

   If a state method has several arguments, they should be listed in an array
   (see "state three" in the example below).
   If a state method has only one argument,
   it can be presented w/o a wrapping array (see "state_four" in the example below):

   .. code-block:: php

        // array representing states for "createOne()" and "createMany()" methods
        [
            'state_one',
            'state_two',
            'state_three' => ['argument_1', 'argument_2'],
            'state_four' => 10,
        ]


.. code-block:: php

    // in CustomerStory.php

    use Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService;
    use Zenstruck\Foundry\Story;

    class CustomerStory extends Story
    {
        private $persistService;

        /**
         * If your story class extends
         * "Voltel\ExtraFoundryBundle\Foundry\Story\AbstractStory"
         * the "EntityProxyPersistService" will be injected automatically
         * with standard Symfony configuration
         */
        public function __construct(EntityProxyPersistService $persistService)
        {
            $this->persistService = $persistService;
        }


        public function build(): void
        {
            $this->createCustomer();
        }


        private function createCustomer()
        {
            // Create a factory that won't immediately persist
            $customer_factory = CustomerFactory::new()->withoutPersisting();
            //
            // Although not explicitly used here, "withoutPersisting()"
            // will be automatically invoked for this factory
            // in persistService before creating entities
            $address_factory = AddressFactory::new();

            for ($i = 0; $i < 20; $i++) {
                $n_address_count = rand(1, 3);

                $this->createCustomerWithAddresses($customer_factory, $address_factory, $n_address_count);
            }

            // Persist and flush new entities as a batch operation.
            // This takes less time than persisting/flushing each entity
            $this->persistService->persistAndFlushAll();
        }


        private function createCustomerWithAddresses(
            CustomerFactory $customer_factory,
            AddressFactory $address_factory,
            int $n_address_count = 1
        )
        {
            /** @var Customer $customer_entity */
            $customer_entity = $this->persistService->createOne($customer_factory);

            for ($i = 0; $i < $n_address_count; $i++) {
                /** @var Address $address_entity */
                $address_entity = $this->persistService->createOne($address_factory);

                $address_entity->setCustomer($customer_entity);
                //
                // or use a specialized collection manipulation method
                // $customer_entity->addElementToAddressCollection($address_entity);

            }
        }

    }


The :method:`EntityProxyPersistService:createOne()` method is responsible for several things:
    * Applying some custom factory states to the factory stub, if provided.
      An array of state names is optional
      and can be passed as the third (last) argument.
    * Factory will be cloned to avoid immediate persistence,
      i.e. :method:`Zenstruck\Foundry\Factory::withoutPersisting()` method
      is going to be invoked.
    * An array of optional arguments for new entities, if provided,
      will be  directly passed as an argument
      to :method:`Zenstruck\Foundry\Factory::create()` method.
    * A new |Proxy| object returned by :method:`Factory:create()`
      will be internally put in a "proxy jar"
      to be later persisted by corresponding entity manager.
      Persisting entities in batches speeds up the whole process.


Create **batches** of entities with delayed persist/flush
----------------------------------------------------------------------

Use :method:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService::createMany()`
method to create several entities at once.
Similar to :method:`EntityProxyPersistService::createOne()`,
pass the factory stub as the first argument
and the number of entities to create as the second argument.

This method may conveniently be modified with the third parameter,
that can accept either an array of attributes or, more importantly,
a callback that returns an array of attributes.
This enables random values for every of the created entities.

You can find examples of using a callback with ``createMany()``
in several sections of the ``zenstruck/foundry`` docs,
e.g. in `Using with DoctrineFixtureBundle`_ and `Many-To-One`_ sections.
Here is an example from test suite of this bundle:

.. code-block:: php

    // in OrderStory.php

    private function createOrderItemsForOrder(Order $order_entity)
    {
        $factory_order_item = OrderItemFactory::new()
            //->withoutPersisting()
            ->forOrder($order_entity);

        // This will create a batch of OrderItem entities,
        // each with its unique "unitCount" value
        //
        $this->persistService->createMany($factory_order_item, rand(1, 4), function() {
            return [
                'unitCount' => rand(1, 20)
            ];
        });
    }

I refer to the first argument as a "factory stub"
because that the factory can be further modified
by passing the fourth (last) argument -- an array of state names (states)
or a callback returning such an array.

States are just method names in the model factory class
that will be invoked on the factory with the result
that the factory will be cloned with new attributes
as described in `Reusable Model Factory "States"`_.


Setting relationships between entities
======================================================================

The ``zenstruck/foundry`` library makes it easy to create related entities
of ``@ORM\ManyToOne`` associations right from inside the factory class
(i.e. by providing a factory for a particular entity property,
as described under `TIP 2` in `Many-To-One`_ section).

.. code-block:: php

    // in OrderItemFactory.php

    protected function getDefaults(): array
    {
        $faker = self::faker();

        return [
            'notes' => $faker->realText(),

            // To randomly assign a product for this order item.
            // Products must be seeded in the database
            // before orders and order items.
            'product' => ProductFactory::repository()->random(),
        ];
    }

The same is possible for `Many-To-Many`_ relationship.



I prefer to have this logic outlined inside a story,
where related entities are created and referenced explicitly:

.. _relationships_with_collection_adder:

.. code-block:: php

    // in ProductStory.php

    private function createProduct(ProductFactory $factory, int $n_entity_count = 20)
    {
        $repo_category = CategoryFactory::repository();

        for ($i = 0; $i < $n_entity_count; $i++) {
            /** @var Product $product */
            $product = $this->persistService->createOne($factory);

            // Add from 1 to 3 categories to each Product
            // "Product" and "Category" have a Many-To-Many relationship
            $a_category_proxies = $repo_category->randomRange(1, 3);

            foreach ($a_category_proxies as $oThisCategoryProxy) {
                /** @var Category $oThisCategoryEntity */
                $oThisCategoryEntity = $oThisCategoryProxy->object();

                $product->addElementToCategoryCollection($oThisCategoryEntity);
            }
        }
    }

The example above could be rewritten to be more succinct,
as described in `Many-To-Many`_ section:

.. code-block:: php

    // in ProductStory.php

    private function createProduct(ProductFactory $factory, int $n_entity_count = 20)
    {
        $repo_category = CategoryFactory::repository();

        $this->persistService->createMany($factory, $n_entity_count, function() use ($repo_category) {
            return [
                'categoryCollection' => $repo_category->randomRange(1, 3),
            ];
        });

    }

.. note:: In the code example above,
   if there is no "setter" for "categoryCollection" property,
   the factory should use a custom instantiator to "force-set" it.

   This can only be a solution for **unidirectional** associations like in this example,
   where :entity:`Product` holds a `unidirectional @ORM\\Many-To-Many`_ association
   with ``Category`` entities.

   For **bidirectional** associations,
   you will most likely need a more sophisticated setter
   that will establish the opposite side of the relationship.
   For example, one :entity:`Customer` can be related to many :entity:`Address` entities,
   and each :entity:`Address` entity is related to one :entity:`Customer`
   (`bidirectional @ORM\\One-To-Many`_ associations).
   In this case, the setter for ``Customer::addressCollection`` property
   could look like this:

   .. code-block:: php

        // in Customer.php

        /**
         * @param Address[]|null $addresses
         * @return Customer
         */
        public function setAddressCollection(?array $addresses): Customer
        {
            $this->addressCollection = new ArrayCollection($addresses);
            foreach ($addresses as $address) {
                $address->setCustomer($this);
            }
            return $this;
        }

   Alternatively, you could use a specialized collection manipulation method
   similar to the ``addElementToCategoryCollection()`` method
   (usage is shown in the :ref:`example above <relationships_with_collection_adder>`):

   .. code-block:: php

        // in Customer.php

        public function addElementToAddressCollection(Address $address)
        {
            if ($this->addressCollection->contains($address)) return;
            $this->addressCollection->add($address);
            $address->setCustomer($this);
        }

.. _states_for_arrays:

Using factory states to populate arrays and establish relationships between entities
=====================================================================================

While `Reusable Model Factory "States"`_ is a great way to set model attributes
in a more explicit way in terms of readability,
with ``zenstruck/foundry`` it is not yet possible to manipulate array values,
particularly, to add individual values to arrays using states.

If you extended your factory class from |AbstractFactory| class,
you will have a :method:`AbstractFactory::addValuesTo()` method at your disposal.
This method can be used to do exactly what it says:
add values to an array stored in a custom model attribute.

Let's see an example (it can be found in a |ProductFactory| class):

.. code-block:: php

    // in ProductFactory.php

    public function car(): self
    {
        return $this->addState([
            // Note: this will add two values to existing values of "categories" attribute
            'categories' => $this->addValuesTo('categories', ['car', 'vehicle']),
        ]);
    }

As :method:`ModelFactory::addState()` method will create a clone of current factory,
normally, the state that modifies some attribute
will override all previous values,
a behavior that is not sometimes desirable.
So, :method:`AbstractFactory::addValuesTo()` method will take
the previous value of the attribute with the name passed in the first argument,
and modify it to be an array holding all previous values and the new values,
passed in the array in the second argument.

Imagine, you modified your :class:`ProductFactory` with two states: ``car`` and ``luxury``:

.. code-block::php

    // in Story class or in TestCase class

    $product_factory = ProductFactory::new();

    // states are provided in the last third argument
    $product = $this->persistService->createOne($product_factory, ['productName' => 'Tesla'], ['luxury', 'car']);


The ``luxury`` state is similar to the ``car`` state and might look like this:

.. code-block:: php

    // in ProductFactory.php

    public function luxury(): self
    {
        return $this->addState([
            // Note: this will add a "luxury" value to existing values of "categories" attribute
            'categories' => $this->addValuesTo('categories', ['luxury']),
        ]);
    }

With this setup, by the time you instantiate your :entity:`Product` entity,
the attributes will look like this:

.. code-block:: php

    // in ProductFactory.php

    protected function initialize()
    {
        return $this
            ->instantiateWith((new Instantiator())
                ->allowExtraAttributes(['categories'])
            )
            ->beforeInstantiate(function($attributes) {
                // $attributes['categories'] => ['luxury', 'car', 'vehicle']
                return $attributes;
            })

Then, in the :method:`afterInstantiate` callback,
you can find those specific :entity:`Category` entities
in the database and assign them to the :entity:`Product`:

.. code-block:: php

    // in ProductFactory.php

    protected function initialize()
    {
        // ...

        $this->afterInstantiate(function(Product $product, $attributes) {
            // If explicit category names were assigned by factory states,
            // find related categories and assign to the product
            if (!empty($attributes['categories'])) {
                foreach ((array) $attributes['categories'] as $c_this_category) {
                    $category_proxy = CategoryFactory::findOrCreate([
                        'categoryName' => $c_this_category
                    ]);

                    /** @var Category $category_entity */
                    $category_entity = $category_proxy->object();

                    $product->addElementToCategoryCollection($category_entity);
                }
            }
        });

        // ...

        return $this;
    }

When your setup will do just fine with random categories
assigned to :entity:`Product` entities, there are obviously
simpler ways to fetch random :entity:`Category` entities
and set them on :property:`Product::categoryCollection`.
But when you need some specific product categories,
moving this logic from stories into a model factory itself
feels like a better alternative,
and using states for this task makes it even more elegant.
With just one line of code, you can create a batch of entities
and establish some of the relationships "in one go".

.. code-block:: php

    // in story or test class

    public function createLuxuryCars()
    {
        $factory_product_stub = ProductFactory::new();

        // create 20 Product entities in categories "luxury", "car" and "vehicle" with random "productName"
        $this->persistService->createMany($factory_product_stub, 20, function(Generator $faker) {
            return [
                'productName' => $faker->randomElement([
                    '2021 Porsche Boxster', '2021 Genesis G80', '2021 Volvo S90', '2021 BMW 7 Series',
                    '2021 Chevrolet Corvette', '2021 Audi TT', '2021 Audi A5', '2020 Mercedes-Benz SL',
                    '2021 Genesis G90', '2020 Kia K900', '2020 Mercedes-Benz E-Class',
                    '2020 Audi R8', '2020 Mercedes-Benz S-Class',
                ]),
            ];
        }, ['luxury', 'car', 'recent', 'promoted']);

        $this->persistService->persistAndFlushAll();
    }

.. note:: In the example above, states ``recent`` and ``promoted``
   will modify model attributes (``registeredAt`` and ``inPromotion``, respectively),
   and states ``luxury`` and ``car`` will add values to a custom attribute ``categories``
   which is used in :method:`ProductFactory::afterInstantiate()` callback
   to find related :entity:`Category` entities in the database
   and assign them to the products.



.. Links:

.. _`DoctrineFixturesBundle`: https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html

.. _`Stories`: https://github.com/zenstruck/foundry/tree/v1.9.1#stories

.. _`Model Factories`: https://github.com/zenstruck/foundry/tree/v1.9.1#model-factories

.. _`Reusable Model Factory "States"`: https://github.com/zenstruck/foundry/tree/v1.9.1#reusable-model-factory-states

.. _`Many-To-One`: https://github.com/zenstruck/foundry/tree/v1.9.1#many-to-one

.. _`Many-To-Many`: https://github.com/zenstruck/foundry/tree/v1.9.1#many-to-many

.. _`Attributes`: https://github.com/zenstruck/foundry/tree/v1.9.1#attributes

.. _`Using with DoctrineFixtureBundle`: https://github.com/zenstruck/foundry/tree/v1.9.1#using-with-doctrinefixturesbundle

.. _`unidirectional @ORM\\Many-To-One`: https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional

.. _`bidirectional @ORM\\One-To-Many`: https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional

.. _`unidirectional @ORM\\Many-To-Many`: https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-many-unidirectional

.. Bundle classes references:

.. |bundle| replace:: `VoltelExtraFoundryBundle`

.. |EntityProxyPersistService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService`

.. |s_EntityProxyPersistService| replace:: :class:`EntityProxyPersistService`

.. |SetUpFixtureEntityService| replace:: :class:`Voltel\ExtraFoundryBundle\Service\FixtureEntity\SetUpFixtureEntityService`
.. |s_SetUpFixtureEntityService| replace:: :class:`SetUpFixtureEntityService`

.. |AbstractStory| replace:: :class:`Voltel\ExtraFoundryBundle\Foundry\Story\AbstractStory`

.. |AbstractFactory| replace:: :class:`Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory`

.. |ProductFactory| replace:: :class:`Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory`

.. |Proxy| replace:: :class:`Zenstruck\Foundry\Proxy`

.. |ModelFactory| replace:: :class:`Zenstruck\Foundry\ModelFactory`

.. |Story| replace:: :class:`Zenstruck\Foundry\Story`

.. |Factory| replace:: :class:`Zenstruck\Foundry\Factory`

.. |Fixture| replace:: :class:`Doctrine\Bundle\FixturesBundle\Fixture`

