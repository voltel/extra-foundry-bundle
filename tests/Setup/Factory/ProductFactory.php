<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Factory;

use Faker\Generator;
use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Product;
use Voltel\ExtraFoundryBundle\Tests\Setup\Service\ProductSlugProviderService;
use Zenstruck\Foundry\Instantiator;

class ProductFactory extends AbstractFactory
{
    protected static $className = Product::class;

    private ProductSlugProviderService $slugProvider;

    public function __construct(
        ProductSlugProviderService $slugProvider
    )
    {
        parent::__construct();

        $this->slugProvider = $slugProvider;
    }


    protected function getDefaults(): array
    {
        $faker = self::faker();

        return [
            'productName' => $faker->words(3, true),
            'inPromotion' => $faker->boolean(75),
            'registeredAt' => $faker->dateTimeBetween( '-3 days ago', '-5 years ago'),
        ];
    }

    protected function initialize()
    {
        return $this
            ->instantiateWith((new Instantiator())
                //
                ->allowExtraAttributes(['categories'])
                ->alwaysForceProperties([
                    // There is no setter for this property.
                    'registeredAt',
                    //
                    // There is no setter for this property.
                    // At the same time, for demonstration purposes, there will be an attempt to set it by zenstruck/foundry
                    // during execution of [Algorithm 1: use "createMany"], i.e. the following line:
                    // 'categoryCollection' => $repo_category->randomRange(1, 3),
                    // Read at:
                    // https://github.com/zenstruck/foundry/tree/v1.7.0#many-to-many
                    'categoryCollection',
                ]))
            //
            ->beforeInstantiate(function($attributes) {
                // "registeredAt" attribute may be a date string, a \DateTime or \DateTimeImmutable object.
                // Doctrine expects a \DateTimeImmutable object. Otherwise, Doctrine DBAL will throw a "ConverstionException".
                if (is_string($attributes['registeredAt'])) {
                    $attributes['registeredAt'] = new \DateTimeImmutable($attributes['registeredAt']);

                } elseif ($attributes['registeredAt'] instanceof \DateTime) {
                    $attributes['registeredAt'] = \DateTimeImmutable::createFromMutable($attributes['registeredAt']);
                }//endif

                return $attributes;
            })
            //
            ->afterInstantiate(function(Product $product, $attributes) {
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
                    }//endforeach
                }//endif
             })
            //
            // This is just an example of using the "afterPersist" callback to generate some value
            // that depends on the id of the persisted entity. E.g. "path" of parent ids in tree-like hierarchies.
            ->afterPersist(function(Product $product) {
                $c_slug = $this->slugProvider->getSlugForProduct($product);
                if (0 === strlen($c_slug)) {
                    throw new \LogicException(sprintf('Failed to get a slug for Product with id=%s', $product->getId()));
                }//endif

                $product->setSlug($c_slug);
                // This change is expected to be flushed by the bundle's persist service
            })
            ;
    }


    public function promoted(): self
    {
        return $this->addState(['inPromotion' => true]);
    }


    public function unpromoted(): self
    {
        return $this->addState(['inPromotion' => false]);
    }


    public function vintage(): self
    {
        return $this->addState(function(Generator $faker) {
            return [
                'registeredAt' => $faker->dateTimeBetween('5 years ago', '3 years ago'),
            ];
        });
    }


    public function recent(): self
    {
        return $this->addState(function(Generator $faker) {
            return [
                'registeredAt' => $faker->dateTimeBetween(  '2 years ago', '3 days ago'),
            ];
        });
    }


    //<editor-fold desc="Custom array attribute 'categories' to describe related Category entities">
    public function car(): self
    {
        return $this->addState([
            // Note: this will add two values to existing values of "categories" attribute
            'categories' => $this->addValuesTo('categories', [Category::CARS, Category::VEHICLES]),
        ]);
    }


    public function luxury(): self
    {
        return $this->addState([
            // Note: this will add a "Luxury" value to existing values of "categories" attribute
            'categories' => $this->addValuesTo('categories', [Category::LUXURY]),
        ]);
    }


    public function ordinary(): self
    {
        return $this->addState([
            // Note: this will add a "Ordinary" value to existing values of "categories" attribute
            'categories' => $this->addValuesTo('categories', [Category::ORDINARY]),
        ]);
    }


    public function jewelry(): self
    {
        return $this->addState([
            'categories' => $this->addValuesTo('categories', [Category::LUXURY, Category::JEWELRY]),
        ]);
    }


    public function house(): self
    {
        return $this->addState([
            'categories' => $this->addValuesTo('categories', [Category::ACCOMMODATION, Category::HOUSES]),
        ]);
    }


    public function apartment(): self
    {
        return $this->addState([
            'categories' => $this->addValuesTo('categories', [Category::ACCOMMODATION, Category::APARTMENTS]),
        ]);
    }


    public function furniture(): self
    {
        return $this->addState([
            'categories' => $this->addValuesTo('categories', [Category::FURNITURE]),
        ]);
    }
    //</editor-fold>


}