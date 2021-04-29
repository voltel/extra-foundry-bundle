<?php


namespace Voltel\ExtraFoundryBundle\Tests\Setup\Service;


use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Category;
use Voltel\ExtraFoundryBundle\Tests\Setup\Entity\Product;

class ProductSlugProviderService
{
    public function getSlugForProduct(Product $product): string
    {
        $c_suffix = '_' . $product->getId();
        $n_suffix_length = mb_strlen($c_suffix);
        $n_stub_length_max = Product::SLUG_LENGTH_MAX - $n_suffix_length;

        if ($product->getCategoryCollection()->count() > 0) {
            /** @var Category $first_category */
            $first_category = $product->getCategoryCollection()->first();
            $c_stub = $this->prepareString($first_category->getCategoryName(), $n_stub_length_max);

        } else {
            $c_stub = $this->prepareString($product->getProductName(), $n_stub_length_max);
        }//endif

        return $c_stub . $c_suffix;
    }//end of function


    private function prepareString(string $c_name, $n_max_length): string
    {
        if (empty($c_name)) {
            throw new \LogicException(sprintf('Empty string for product slug cannot be prepared. '));
        }

        $c_result = trim($c_name);
        $c_result = preg_replace('/\s+/', '_', $c_result);
        $c_result = mb_strtolower($c_result, 'UTF8');
        $c_result = mb_substr($c_result, 0, $n_max_length);

        return trim($c_result);
    }
}