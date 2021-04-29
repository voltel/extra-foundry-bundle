<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Story;


use Voltel\ExtraFoundryBundle\Foundry\Story\AbstractStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\AddressFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CustomerFactory;

class CustomerStory extends AbstractStory
{
    public const COUNT_CUSTOMER = 20;


    public function build(): void
    {
        $this->createCustomer();
    }

    private function createCustomer()
    {
        $customer_factory_stub = CustomerFactory::new(); //->withoutPersisting()
        $address_factory_stub = AddressFactory::new(); // ->withoutPersisting()

        // Algorithm 1: use "createMany"
        $this->createCustomersWithAddresses($customer_factory_stub, $address_factory_stub);

        // Algorithm 2: create customers one by one in a cycle, for each customer create several addresses
        $this->createCustomers($customer_factory_stub, $address_factory_stub);


        $this->persistService->persistAndFlushAll();
    }//end of function


    /**
     * Creates a batch of Customer entities with random number of Address entities.
     * Note: This algorithm requires a Customer entity to have a specialized "addressCollection" setter
     * Note: This algorithm can't ensure that Address address and Customer names use the same faker locale.
     */
    private function createCustomersWithAddresses(
        CustomerFactory $customer_factory,
        AddressFactory $address_factory
    )
    {
        $this->persistService->createMany($customer_factory, self::COUNT_CUSTOMER,
            function () use ($address_factory) {
                return [
                    // This approach requires that the Customer entity has an "addressCollection" setter
                    // that will set a "customer" property for each Address in the collection
                    'addressCollection' => $this->persistService->createMany($address_factory, rand(1, 3)),
                ];
            },
            // When provided, this callback will be called for each created entity
            // Must return an array of states
            function () {
                return [
                    ['american', 'ukrainian', 'russian'][rand(0, 2)],
                    rand(1, 10) < 7 ? 'human' : 'company',
                ];
            });
    }//end of function


    /**
     * This function will create Customer entities
     * along with some random number of Address entities for each Customer.
     * Unlike the "createCustomersWithAddresses" method above,
     * Customer names and their Address will be in the same language (the same locale will be used by Faker)
     */
    private function createCustomers(
        CustomerFactory $customer_factory_stub,
        AddressFactory $address_factory_stub
    ): void
    {
        for ($i = 0; $i < self::COUNT_CUSTOMER; $i++) {
            $c_locale_state = ['american', 'ukrainian', 'russian'][rand(0, 2)];

            $c_human_or_company_state = rand(1, 10) < 7 ? 'human' : 'company';

            /** @var Customer $customer_entity */
            $customer_entity = $this->persistService->createOne($customer_factory_stub, [], [
                $c_locale_state,
                $c_human_or_company_state
            ]);

            $n_address_count = rand(1, 3);
            $this->persistService->createMany($address_factory_stub, $n_address_count, [
                'customer' => $customer_entity
            ], [
                $c_locale_state
            ]);
        }//endfor
    }//end of function


}
