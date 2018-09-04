<?php

declare(strict_types=1);

namespace Mukhin\SyliusItemsSoldPlugin\Calculator;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemRepository;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;

class DefaultCalculator implements CalculatorInterface
{
    /**
     * @var array
     */
    protected $checkoutStates;

    /**
     * @var array
     */
    protected $paymentStates;

    /**
     * @var array
     */
    protected $shippingStates;

    /**
     * @var int|null
     */
    protected $summarizeInterval;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * DefaultCalculator constructor.
     * @param OrderItemRepository $orderItemRepository
     * @param array $checkoutStates Check at OrderCheckoutStates
     * @param array $paymentStates Check at OrderPaymentStates
     * @param array $shippingStates Check at OrderShippingStates
     * @param int|null $summarizeInterval
     */
    public function __construct(
        OrderItemRepository $orderItemRepository,
        array $checkoutStates,
        array $paymentStates,
        array $shippingStates,
        ?int $summarizeInterval = null
    )
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->checkoutStates = $checkoutStates;
        $this->paymentStates = $paymentStates;
        $this->shippingStates = $shippingStates;
        $this->summarizeInterval = $summarizeInterval;
    }

    /**
     * @param Product $product
     * @return int
     */
    public function summarizeForProduct(Product $product): int
    {
        $queryBuilder = $this->orderItemRepository->createQueryBuilder('orderItem')
            ->select('sum(orderItem.quantity)')
            ->leftJoin('orderItem.order', 'o')
            ->leftJoin('orderItem.variant', 'variant')
            ->andWhere('variant.product = :product')
            ->setParameter('product', $product)
        ;

        return (int)$this->addCommonWheres($queryBuilder)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /**
     * @param ProductVariant $productVariant
     * @return int
     */
    public function summarizeForProductVariant(ProductVariant $productVariant): int
    {
        $queryBuilder = $this->orderItemRepository->createQueryBuilder('orderItem')
            ->select('sum(orderItem.quantity)')
            ->leftJoin('orderItem.order', 'o')
            ->andWhere('orderItem.variant = :variant')
            ->setParameter('variant', $productVariant)
        ;

        return (int)$this->addCommonWheres($queryBuilder)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function addCommonWheres(QueryBuilder $queryBuilder): QueryBuilder
    {
        if ($this->checkoutStates) {
            $queryBuilder
                ->andWhere('o.checkoutState = :checkoutStates')
                ->setParameter('checkoutStates', $this->checkoutStates)
            ;
        }

        if ($this->paymentStates) {
            $queryBuilder
                ->andWhere('o.paymentState = :paymentStates')
                ->setParameter('paymentStates', $this->paymentStates)
            ;
        }

        if ($this->shippingStates) {
            $queryBuilder
                ->andWhere('o.shippingState = :shippingStates')
                ->setParameter('shippingStates', $this->shippingStates)
            ;
        }

        if ($this->summarizeInterval) {
            $fromDate = new \DateTime(sprintf(
                '-%s days',
                $this->summarizeInterval
            ));

            $queryBuilder
                ->andWhere('o.checkoutCompletedAt >= :checkoutCompletedAt')
                ->setParameter('checkoutCompletedAt', $fromDate->format('Y-m-d H:i:s'))
            ;
        }

        return $queryBuilder;
    }
}
