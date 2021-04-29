<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Factory;


use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Order;
use Zenstruck\Foundry\Instantiator;

class OrderFactory extends AbstractFactory
{
    protected static $className = Order::class;

    protected function getDefaults(): array
    {
        $faker = self::faker();

        return [
            'orderedAt' => \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('5 years ago', '1 week ago')),
            'status' => $faker->randomElement(Order::STATUSES),
        ];
    }

    protected function initialize()
    {
        $faker = self::faker();

        return $this
            // The property "orderedAt" is private and there is no public setter, so use the force setter
            ->instantiateWith((new Instantiator())->alwaysForceProperties(['orderedAt']))
            //
            ->beforeInstantiate(function(array $arguments) use ($faker) {
                if (empty($arguments['customer']) || !($arguments['customer'] instanceof Customer)) {
                    throw new \LogicException(sprintf('Expected to have an Customer entity in the factory arguments.'));
                }//endif

                /** @var Customer $customer */
                $customer = $arguments['customer'];
                $arguments['deliveryAddress'] = $faker->randomElement($customer->getAddressCollection()->toArray());
                if (rand(1, 10) > 5) {
                    $arguments['billingAddress'] = $faker->randomElement($customer->getAddressCollection()->toArray());
                }//endif

                if ($arguments['status'] === Order::STATUS_CANCELLED) {
                    $arguments['deliveredAt'] = null;
                }//endif

                return $arguments;
            })
            //
            ->afterInstantiate(function(Order $order) use ($faker) {
                if ($order->getStatus() === Order::STATUS_DELIVERED) {
                    if (is_null($order->getOrderedAt())) {
                        throw new \LogicException(sprintf('Expected to have a not-null value for "orderedAt" property. '));
                    }
                    $d_delivered_date = $faker->dateTimeBetween(\DateTime::createFromImmutable($order->getOrderedAt()),'1 day ago');
                    $order->setDeliveredAt($d_delivered_date);
                }
            })
        ;
    }


    public function forCustomer(Customer $customer): self
    {
        return $this->addState(['customer' => $customer]);
    }


    public function created(): self
    {
        return $this->addState(['status' => 'created']);
    }


    public function checkedout(): self
    {
        return $this->addState(['status' => 'checkedout']);
    }


    public function sent(): self
    {
        return $this->addState(['status' => 'sent']);
    }


    public function awaiting(): self
    {
        return $this->addState(['status' => 'awaiting']);
    }


    public function delivered(): self
    {
        return $this->addState(['status' => 'delivered']);
    }


    public function cancelled(): self
    {
        return $this->addState(['status' => 'cancelled']);
    }

}