<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;

interface WishlistFromRequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return WishlistInterface
     */
    public function resolve(Request $request) : WishlistInterface;
}
