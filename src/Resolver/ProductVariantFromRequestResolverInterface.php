<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request;

interface ProductVariantFromRequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return ProductVariantInterface
     */
    public function resolve(Request $request) : ProductVariantInterface;
}
