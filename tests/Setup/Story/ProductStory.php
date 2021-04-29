<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Story;


use Faker\Generator;
use Voltel\ExtraFoundryBundle\Foundry\Story\AbstractStory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Product;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\CategoryFactory;
use Voltel\ExtraFoundryBundle\Tests\Setup\Factory\ProductFactory;

class ProductStory extends AbstractStory
{
    public const COUNT_GENERIC_PRODUCTS = 20;
    public const COUNT_LUXURY_CARS = 20;

    public function build(): void
    {
        $this->createCategories();

        $this->createProducts();
        $this->createLuxuryCars();
    }

    private function createCategories()
    {
        $factory_category = CategoryFactory::new()->withoutPersisting();

        foreach (Category::CATEGORIES as $c_this_category_name) {
            $this->persistService->createOne($factory_category, ['categoryName' => $c_this_category_name]);
        }//endforeach

        $this->persistService->persistAndFlushAll();
    }//end of function


    private function createProducts()
    {
        $factory_product_stub = ProductFactory::new();//->withoutPersisting();

        // Create "vintage" products
        $n_vintage_product_count = intval(floor(self::COUNT_GENERIC_PRODUCTS * .75));
        $this->createProduct($factory_product_stub, $n_vintage_product_count, ['vintage']);

        // Create "recent" products
        $n_recent_product_count = self::COUNT_GENERIC_PRODUCTS - $n_vintage_product_count;
        $this->createProduct($factory_product_stub, $n_recent_product_count, ['recent']);

        $this->persistService->persistAndFlushAll();
    }//end of function


    private function createProduct(
        ProductFactory $factory_stub,
        int $n_entity_count,
        array $a_states = []
    )
    {
        // Note: product categories must already exist in the database
        $repo_category = CategoryFactory::repository();

        // We will use two algorithms, one after the other, for demonstration purposes

        // Algorithm 1: use "createMany"
        $this->persistService->createMany($factory_stub, $n_entity_count, function() use ($repo_category) {
            return [
                // Note: this requires property "categoryCollection" to be force-set (see ProductFactory)
                'categoryCollection' => $repo_category->randomRange(1, 3),
            ];
        }, $a_states);


        // Algorithm 2: use "createOne" in cycle
        for ($i = 0; $i < $n_entity_count; $i++) {
            /** @var Product $product */
            $product = $this->persistService->createOne($factory_stub, [], $a_states);

            // Add from 1 to 3 categories to each Product
            $a_category_proxies = $repo_category->randomRange(1, 3);
            foreach ($a_category_proxies as $oThisCategoryProxy) {
                /** @var Category $oThisCategoryEntity */
                $oThisCategoryEntity = $oThisCategoryProxy->object();
                $product->addElementToCategoryCollection($oThisCategoryEntity);
            }//endforeach

        }//endfor

    }//end of function


    public function createLuxuryCars()
    {
        $factory_product_stub = ProductFactory::new();

        $this->persistService->createMany($factory_product_stub, self::COUNT_LUXURY_CARS, function(Generator $faker) {
            return [
                'productName' => $faker->randomElement([
                    '2021 Porsche Boxster', '2021 Genesis G80', '2021 Volvo S90', '2021 BMW 7 Series',
                    '2021 Chevrolet Corvette', '2021 Audi TT', '2021 Audi A5', '2020 Mercedes-Benz SL',
                    '2021 Genesis G90', '2020 Kia K900', '2020 Mercedes-Benz E-Class',
                    '2020 Audi R8', '2020 Mercedes-Benz S-Class',
                ]),
            ];
        }, ['luxury', 'car', 'recent', 'promoted']);

        $this->persistService->persistAndFlushAll();
    }//end of function

}
