<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Factory;

use Faker\Generator;
use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;

class CategoryFactory extends AbstractFactory
{
    protected static $className = Category::class;

    private Generator $customFaker;

    public function __construct(
        Generator $customFaker
    )
    {
        parent::__construct();
        $this->customFaker = $customFaker;
    }

    protected function getDefaults(): array
    {
        //$faker = self::faker();
        $faker = $this->customFaker;

        return [
            'categoryName' => $faker->word(),
        ];
    }

}