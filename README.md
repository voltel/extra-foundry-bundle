## VoltelExtraFoundryBundle 

This bundle is a wrapper for [zenstruck/foundry](https://github.com/zenstruck/foundry) bundle.

It features:
- **delayed persistence** to save time during development database seeding;
- MySQL **dump file loader** to re-create initial database state 
  in "test" environment and minimize testing time;
- a set-up service to **create entities from _blueprints_** 
  which is a convenience tool to use in tests (e.g. with data providers) 
  or while seeding the database in "dev" environment;
- a number of "improvements" on the way [zenstruck/foundry](https://github.com/zenstruck/foundry) 
  does things.   
  
 
Documentation 
==============

Read the docs at https://voltelextrafoundrybundle.readthedocs.io/en/latest/.

Source files for documentation live in the **"docs/"** folder of the bundle 
in **reStructuredText** format files (*.rst).

Usage
============
For usage examples, [read the docs](https://voltelextrafoundrybundle.readthedocs.io/en/latest/)
and see the source code in the testing suite (in "test/Setup" directory of the bundle).

Seeding the development database: 
---------------------------------
```php
    $order_entity = $this->persistService->createOne($factory_order, 
        ['customer' => $customer_entity], 
        ['sent']
    );

    $order_item_batch = $this->persistService->createMany($factory_order_item, rand(1, 4), function() {
        return [
            'unitCount' => rand(1, 20)
        ];
    });
```

Creating entities from *blueprint*: 
------------------------------------
```php
        $a_setup_customers = [
            'customer_1' =>['states' => ['american', 'human']],
            'customer_2' =>['states' => ['ukrainian', 'human'],
            'customer_3' =>['states' => ['human'], 'attributes' => ['firstName' => 'John', 'lastName' => 'Doe']],
            'customer_4' =>['states' => ['human'], 'attributes' => ['firstName' => 'Богдан', 'lastName' => 'Мірошник']],
        ];

        $this->setUpFixtureEntityService-> createEntities($factory_customer, $a_setup_customers, true);
```

Load Global State from MySQL dump file:
----------------------------------------
```php
    class GlobalStory extends Story
    {
        //...

        public function build(): void
        {
            $this->sqlDumpLoaderService->loadSqlDump();
        }
    }
```

You will find many more [examples in the docs](https://voltelextrafoundrybundle.readthedocs.io/en/latest/) 
and in the "tests/" directory of the bundle. 

Contributing
============
    Of course, open source is fueled by everyone's ability to give just a little bit
    of their time for the greater good. If you'd like to see a feature or add some of
    your *own* happy words, awesome! You can request it - but creating a pull request
    is an even better way to get things done.
    
    Either way, please feel comfortable submitting issues or pull requests: all contributions
    and questions are warmly appreciated :).
    
That beeing said, you may find it more appropriate to 
**contribute to the zenstruck/foundry project** itself, 
as this bundle is only a convenience wrapper. 
