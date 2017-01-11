<?php

namespace Webburza\Sylius\WishlistBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

interface WishlistRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @param UserInterface $user
     *
     * @return integer
     */
    public function getCountForUser(UserInterface $user);

    /**
     * Get the first wishlist for the user, if any.
     *
     * @param UserInterface $user
     *
     * @return WishlistInterface|object|null
     */
    public function getFirstForUser(UserInterface $user);
}
