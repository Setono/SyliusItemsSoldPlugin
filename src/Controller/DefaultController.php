<?php

declare(strict_types=1);

namespace Setono\SyliusItemsSoldPlugin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function productSoldAction(Request $request, int $id): Response
    {
        return new Response(
            $this->container->get('setono_sylius_items_sold.calculator')->summarizeForProduct(
                $this->container->get('sylius.repository.product')->find($id)
            )
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function productVariantSoldAction(Request $request, int $id): Response
    {
        return new Response(
            $this->container->get('setono_sylius_items_sold.calculator')->summarizeForProductVariant(
                $this->container->get('sylius.repository.product_variant')->find($id)
            )
        );
    }
}
