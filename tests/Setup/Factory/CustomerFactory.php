<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Factory;

use Faker\Generator;
use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Customer;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\Instantiator;

class CustomerFactory extends AbstractFactory
{
    protected static $className = Customer::class;

    private Generator $fakerUs;
    private Generator $fakerUa;
    private Generator $fakerRu;

    public function __construct(
        Generator $fakerUs,
        Generator $fakerUa,
        Generator $fakerRu
    )
    {
        parent::__construct();

        $this->fakerUs = $fakerUs;
        $this->fakerUa = $fakerUa;
        $this->fakerRu = $fakerRu;
    }

    protected function getDefaults(): array
    {

        return [
            // Note: If the following defaults are provided here,
            //  they won't be overridden in initialize::beforeInstantiate method
            //'isOrganization' => false,
            //'firstName' => 'Default',
            //'lastName' => 'Default',
            //
            // Note: If default is a factory, no delayed batch persistence will happen
            // 'addressCollection' => AddressFactory::new()->many(1, 3)
        ];
    }

    protected function initialize()
    {
        return $this
            ->instantiateWith((new Instantiator())
                ->allowExtraAttributes(['locale'])
            )
            ->beforeInstantiate(function(array $attributes)
            {
                $faker = self::faker();

                if (!empty($attributes['locale'])) {
                    if ($attributes['locale'] === 'en_US') $faker = $this->fakerUs;
                    elseif ($attributes['locale'] === 'uk_UA') $faker = $this->fakerUa;
                    elseif ($attributes['locale'] === 'ru_RU') $faker = $this->fakerRu;
                }//endif

                $attributes['isOrganization'] = $attributes['isOrganization'] ?? $faker->boolean(50);
                //$attributes['staffCount'] = $attributes['staffCount'] ?? null;
                $attributes['firstName'] = $attributes['firstName'] ?? ($attributes['isOrganization'] ? $faker->company() : $faker->firstName());
                $attributes['lastName'] = $attributes['lastName'] ?? ($attributes['isOrganization'] ? null: $faker->lastName());

                return $attributes;
            })
        ;
    }

    public function human():self
    {
        return $this->addState([
            'isOrganization' => false,
        ]);
    }

    public function company():self
    {
        return $this->addState([
            'isOrganization' => true,
            'lastName' => null
        ]);
    }

    /**
     * USAGE:
     * withStaffCount(3) - company will have exactly 3 employees
     * withStaffCount(1, 5) - company will have a random number of employees - from 1 to 5
     */
    public function withStaffCount(int $n_staff_count, int $n_max_count_range = null):self
    {
        if (!is_null($n_max_count_range) && $n_max_count_range <= $n_staff_count) {
            throw new \InvalidArgumentException(sprintf('Expected max count of employees to be greater than "%s". Got "%s"',
                $n_staff_count, $n_max_count_range));
        }//endif

        // If only the first argument is provided, return an array (not a callback) since there is no variability.
        // If a range is defined, return a callback function which will be called for each entity separately.
        return $this->addState(is_null($n_max_count_range) ?
            ['staffCount' =>  $n_staff_count] :
            function() use($n_staff_count, $n_max_count_range) {
                return ['staffCount' => rand($n_staff_count, $n_max_count_range)];
            });
    }//end of function


    public function american():self
    {
        return $this->addState(['locale' => 'en_US']);
    }

    public function ukrainian():self
    {
        return $this->addState(['locale' => 'uk_UA']);
    }

    public function russian():self
    {
        return $this->addState(['locale' => 'ru_RU']);
    }

}