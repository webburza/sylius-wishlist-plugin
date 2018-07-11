<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;

interface WishlistRepositoryInterface extends RepositoryInterface
{
    /*
     * @return QueryBuilder
     */
    public function createListQueryBuilder() : QueryBuilder;

    /**
     * @param ShopUserInterface $user
     *
     * @return int
     */
    public function getCountForUser(ShopUserInterface $user) : int;

    /**
     * Get the first wishlist for the user, if any.
     *
     * @param ShopUserInterface $user
     *
     * @return WishlistInterface|object|null
     */
    public function getFirstForUser(ShopUserInterface $user) : ?WishlistInterface;
}
