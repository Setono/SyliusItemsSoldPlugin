<?php

declare(strict_types=1);

namespace Mukhin\SyliusItemsSoldPlugin\Calculator;

use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;

interface CalculatorInterface
{
    /**
     * @param Product $product
     * @return int
     */
    public function summarizeForProduct(Product $product): int;

    /**
     * @param ProductVariant $productVariant
     * @return int
     */
    public function summarizeForProductVariant(ProductVariant $productVariant): int;
}
