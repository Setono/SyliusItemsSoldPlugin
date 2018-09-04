<?php

declare(strict_types=1);

namespace Mukhin\SyliusItemsSoldPlugin\DependencyInjection;

use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mukhin_sylius_items_sold');
        $rootNode
            ->children()
                ->arrayNode('cache')
                    ->children()
                        ->scalarNode('service')
                            ->info('Cache service name, should implement Psr\SimpleCache\CacheInterface')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('ttl')
                            ->info("Cache TTL in seconds to override default cache service TTL")
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('interval')
                    ->info('Calculate sold items count for last [interval] days only, if provided')
                    ->defaultNull()
                ->end()
                ->arrayNode('checkout_states')
                    ->treatFalseLike([])
                    ->treatNullLike([])
                    ->prototype('scalar')->end()
                    ->info('What checkout states should be included in calculations')
//                    ->defaultValue([
//                        OrderCheckoutStates::STATE_ADDRESSED,
//                        OrderCheckoutStates::STATE_CART,
//                        OrderCheckoutStates::STATE_COMPLETED,
//                        OrderCheckoutStates::STATE_PAYMENT_SELECTED,
//                        OrderCheckoutStates::STATE_PAYMENT_SKIPPED,
//                        OrderCheckoutStates::STATE_SHIPPING_SELECTED,
//                    ])
                ->end()
                ->arrayNode('payment_states')
                    ->treatFalseLike([])
                    ->treatNullLike([])
                    ->prototype('scalar')->end()
                    ->info('What payment states should be included in calculations')
//                    ->defaultValue([
//                        OrderPaymentStates::STATE_CART,
//                        OrderPaymentStates::STATE_AWAITING_PAYMENT,
//                        OrderPaymentStates::STATE_PARTIALLY_PAID,
//                        OrderPaymentStates::STATE_CANCELLED,
//                        OrderPaymentStates::STATE_PAID,
//                        OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
//                        OrderPaymentStates::STATE_REFUNDED,
//                    ])
                ->end()
                ->arrayNode('shipping_states')
                    ->treatFalseLike([])
                    ->treatNullLike([])
                    ->prototype('scalar')->end()
                    ->info('What checkout states should be included in calculations')
//                    ->defaultValue([
//                        OrderShippingStates::STATE_CART,
//                        OrderShippingStates::STATE_READY,
//                        OrderShippingStates::STATE_CANCELLED,
//                        OrderShippingStates::STATE_PARTIALLY_SHIPPED,
//                        OrderShippingStates::STATE_SHIPPED,
//                    ])
                ->end()
            ->end()
            ;
        return $treeBuilder;
    }
}
