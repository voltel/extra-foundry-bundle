<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Factory;

use Faker\Generator;
use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Address;
use Zenstruck\Foundry\Instantiator;

class AddressFactory extends AbstractFactory
{
    protected static $className = Address::class;

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
        $faker = self::faker();

        return [
            'countryCode' => $faker->countryCode(),
            'cityName' => $faker->city(),
            'cityAreaName' => $faker->citySuffix(),
            'addressName' => $faker->address(),
        ];
    }

    protected function initialize()
    {
        return $this
            ->instantiateWith((new Instantiator())->allowExtraAttributes(['locale']))
            ->beforeInstantiate(function(array $attributes): array
            {
                $faker = self::faker();

                if (!empty($attributes['locale'])) {
                    if ($attributes['locale'] === 'en_US') $faker = $this->fakerUs;
                    elseif ($attributes['locale'] === 'uk_UA') $faker = $this->fakerUa;
                    elseif ($attributes['locale'] === 'ru_RU') $faker = $this->fakerRu;
                }//endif

                return array_merge($attributes, [
                    'cityName' => $faker->city(),
                    'cityAreaName' => $faker->citySuffix(),
                    'addressName' => $faker->address(),
                ]);
            })
        ;
    }

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