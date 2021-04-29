<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Factory;


use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Order;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\OrderItem;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Product;
use Zenstruck\Foundry\Proxy;

class OrderItemFactory extends AbstractFactory
{
    protected static $className = OrderItem::class;

    protected function getDefaults(): array
    {
        $faker = self::faker();

        return [
            'notes' => $faker->realText(),
            'product' => ProductFactory::repository()->random(),
        ];
    }

    protected function initialize()
    {
        return $this
            ->beforeInstantiate(function(array $arguments) {
                if (empty($arguments['order'])) {
                    throw new \LogicException(sprintf('Expected to have an Order entity in the factory arguments.'));
                }//endif

                if (empty($arguments['product'])) {
                    var_dump($arguments);
                    throw new \LogicException(sprintf('Expected to have a Product entity in the factory arguments.'));
                }//endif

                if (empty($arguments['unitCount'])) {
                    throw new \LogicException(sprintf('Expected to have an integer for unitCount of Product in the OrderItem in the factory arguments.'));
                }//endif

                return $arguments;
            })
            ;
    }


    public function forOrder(Order $order): self
    {
        return $this->addState(['order' => $order]);
    }

}