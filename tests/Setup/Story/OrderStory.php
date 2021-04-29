<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Story;


use Voltel\ExtraFoundryBundle\Foundry\Story\AbstractStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Order;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CustomerFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\OrderItemFactory;

class OrderStory extends AbstractStory
{
    private const COUNT_ITEMS_PER_ORDER_MAX = 4;
    private const COUNT_ORDERS_PER_CUSTOMER_MAX = 4;



    public function build(): void
    {
        $this->createOrders();
    }

    private function createOrders()
    {
        $factory_order = OrderFactory::new()->withoutPersisting();

        $repo_customer = CustomerFactory::repository();
        $a_customer_proxy_batch = $repo_customer->findAll();

        foreach ($a_customer_proxy_batch as $o_this_customer_proxy) {
            /** @var Customer $customer_entity */
            $customer_entity = $o_this_customer_proxy->object();
            $n_orders_for_this_customer = rand(1, self::COUNT_ORDERS_PER_CUSTOMER_MAX);

            for ($i = 0; $i < $n_orders_for_this_customer; $i++) {
                /** @var Order $order_entity */
                $order_entity = $this->persistService->createOne($factory_order, ['customer' => $customer_entity]);

                $this->createOrderItemsForOrder($order_entity);
            }//endfor
        }//end foreach

        $this->persistService->persistAndFlushAll();
    }//end of function


    private function createOrderItemsForOrder(Order $order_entity)
    {
        $factory_order_item = OrderItemFactory::new()->withoutPersisting()
            ->forOrder($order_entity);

        $this->persistService->createMany($factory_order_item, rand(1, self::COUNT_ITEMS_PER_ORDER_MAX), function() {
            return [
                'unitCount' => rand(1, 20)
            ];
        });

    }//end of function

}
