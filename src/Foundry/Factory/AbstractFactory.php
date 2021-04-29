<?php

namespace Voltel\ExtraFoundryBundle\Foundry\Factory;

use Zenstruck\Foundry\ModelFactory;

abstract class AbstractFactory extends ModelFactory
    implements AbstractFactoryInterface
{
    // FQCN of the entity this factory will produce
    protected static $className = null;

    private static $attributeSetReflection;


    public static function getClassName(): string
    {
        return static::$className ?: static::getClass();
    }


    /**
     * Implements abstract method of ModelFactory.
     * May be overridden in the implementing class,
     * instead of defining the class protected static property $className
     * Note: consider changing the method signature to "public"
     *
     * @return string
     */
    protected static function getClass(): string
    {
        if (empty(static::$className)) {
            throw new \LogicException('Did you forget to define "protected static $className" or override the "protected static function getClass()" ?');
        }//endif

        return static::$className;
    }


    protected function createReflectionForProperty(string $c_property_name) : \ReflectionProperty
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflectionProperty = new \ReflectionProperty($this->getClassName(), $c_property_name);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }//end of function


    protected function forceSet(object $object, string $c_property_name, $value)
    {
        $c_fqcn = get_class($object);

        $reflectionProperty = $this->createReflectionForProperty($c_property_name);
        $c_expected_class = $reflectionProperty->class;

        if ($c_expected_class !== $c_fqcn) {
            throw new \LogicException(sprintf('Method "%s" in "%s" can only accept objects of class "%s". You passed object of class "%s". ',
                basename(__METHOD__), static::class, $c_expected_class, $c_fqcn));
        }//endif

        $reflectionProperty->setValue($object, $value);
    }//end of function


    /**
     * Adds values from array in the second argument
     * to previously set values
     * on a custom attribute with name from argument one.
     *
     * This method will return all unique values
     * from previously used states that modified this attribute.
     *
     * USAGE EXAMPLE: Let's assume, we have the "Product" entity factory and it has two states defined:
     * state "efficient" which adds two values to the attribute "categories" (e.g. "time-saving", "cost-saving")
     * and state "pleasing" which also adds two values to the attribute "categories" (e.g. "nice-to-touch" and "nice-to-look").
     *
     * If both states are applied as follows:
     *     // in state "efficient"
     *     return $this->addState(['categories' => $this->addValuesTo('categories', ['time-saving', 'cost-saving']) ]);
     *     // in state "pleasing"
     *     return $this->addState(['categories' => $this->addValuesTo('categories', ['nice-to-touch', 'nice-to-look']) ]);
     * then final attribute "categories" will be an array of all four values:
     * ['time-saving', 'cost-saving', 'nice-to-touch', 'nice-to-look'].
     *
     * This array, can be used in "afterInstantiate" or "afterPersist" callback
     * to fetch from the repository or create new category entities, and assign them to the Product entity.
     */
    protected function addValuesTo(
        string $c_attribute_key,
        array $a_values_to_add
    ): array
    {
        if (func_num_args() > 2) {
            throw new \InvalidArgumentException(sprintf('Method "%s" can take in its second argument either an array of values or a scalar value. You provided more than two parameters. ', __METHOD__ ));
        }//endif

        $attributeSet = self::getAttributeSetPropertyReflection()->getValue($this);

        $a_collection_values = [];
        foreach ($attributeSet as $thisAttribute) {
            if (!empty($thisAttribute[$c_attribute_key])) {
                $a_collection_values = array_merge($a_collection_values, (array) $thisAttribute[$c_attribute_key]);
                // if found, no need to iterate other attributes
                break;
            }//endif
        }//endforeach

        $a_collection_values = array_unique(array_merge($a_collection_values, $a_values_to_add));

        return $a_collection_values;
    }//end of function


    private static function getAttributeSetPropertyReflection(): \ReflectionProperty
    {
        if (null === self::$attributeSetReflection) {
            self::$attributeSetReflection = new \ReflectionProperty(\Zenstruck\Foundry\Factory::class, 'attributeSet');
            self::$attributeSetReflection->setAccessible(true);
        }

        return self::$attributeSetReflection;
    }//end of function

}