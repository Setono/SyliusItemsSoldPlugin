<?php

declare(strict_types=1);

namespace Setono\SyliusItemsSoldPlugin\Calculator;

use Psr\SimpleCache\CacheInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;

class CacheableCalculator implements CalculatorInterface
{
    /**
     * @var CalculatorInterface
     */
    protected $decorated;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var int|null
     */
    protected $ttl;

    /**
     * CacheableCalculator constructor.
     *
     * @param CalculatorInterface $decorated
     * @param CacheInterface $cache
     * @param int|null $ttl
     */
    public function __construct(CalculatorInterface $decorated, CacheInterface $cache, ?int $ttl = null)
    {
        $this->decorated = $decorated;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * @param Product $product
     *
     * @return int
     */
    public function summarizeForProduct(Product $product): int
    {
        $key = $this->buildKey($product);

        return $this->passThroughCache($key, function () use ($product) {
            return $this->decorated->summarizeForProduct($product);
        });
    }

    /**
     * @param ProductVariant $productVariant
     *
     * @return int
     */
    public function summarizeForProductVariant(ProductVariant $productVariant): int
    {
        $key = $this->buildKey($productVariant);

        return $this->passThroughCache($key, function () use ($productVariant) {
            return $this->decorated->summarizeForProductVariant($productVariant);
        });
    }

    /**
     * @param string $key
     * @param callable $callable
     *
     * @return mixed
     */
    private function passThroughCache(string $key, callable $callable)
    {
        if (!$this->cache->has($key)) {
            $this->cache->set($key, call_user_func($callable));
        }

        return $this->cache->get($key);
    }

    /**
     * @param Product|ProductVariant $object
     *
     * @return string
     */
    private function buildKey($object): string
    {
        switch (true) {
            case $object instanceof Product:
                return sprintf('product-%s', $object->getId());
            case $object instanceof Product:
                return sprintf('product-variant-%s', $object->getId());
            default:
                throw new \Exception(sprintf(
                    'Unsupported object to cache: %s. Expecting one of: %s',
                    get_class($object),
                    implode(', ', [Product::class, ProductVariant::class])
                ));
        }
    }
}
