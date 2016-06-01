<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\CustomerInterface;

interface WishlistRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all wishlists for a customer.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function getWishlistsForCustomer(CustomerInterface $customer);

    /**
     * Get wishlist count for a customer.
     *
     * @param CustomerInterface $customer
     * @return integer
     */
    public function getCountForCustomer(CustomerInterface $customer);

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCountQueryBuilder();

    /**
     * Get a wishlist by id, for a customer.
     *
     * @param CustomerInterface $customer
     * @param $id
     * @return WishlistInterface
     */
    public function findForCustomer(CustomerInterface $customer, $id);

    /**
     * Get the first wishlist for the customer, if any.
     *
     * @param CustomerInterface $customer
     * @return WishlistInterface|null
     */
    public function getFirstForCustomer(CustomerInterface $customer);
}
