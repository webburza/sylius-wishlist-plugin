<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Factory;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;

interface WishlistFactoryInterface extends FactoryInterface
{
    /**
     * @param ShopUserInterface $user
     *
     * @return WishlistInterface
     */
    public function createDefault(ShopUserInterface $user) : WishlistInterface;
}
