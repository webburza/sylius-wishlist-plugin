<?php

namespace Webburza\Sylius\WishlistBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\User\Model\CustomerInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistRepositoryInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    /**
     * Get all wishlists for a customer.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function getWishlistsForCustomer(CustomerInterface $customer)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->where('IDENTITY(o.customer) = :customerId');
        $queryBuilder->setParameter('customerId', $customer->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Get wishlist count for a customer.
     *
     * @param CustomerInterface $customer
     * @return integer
     */
    public function getCountForCustomer(CustomerInterface $customer)
    {
        // Get query builder
        $queryBuilder = $this->getCountQueryBuilder();

        // Apply customer condition
        $queryBuilder->where('IDENTITY(o.customer) = :customerId');
        $queryBuilder->setParameter('customerId', $customer->getId());

        // Return record count
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCountQueryBuilder()
    {
        return $this->getEntityManager()->createQueryBuilder()
             ->select('COUNT(o)')
             ->from($this->getEntityName(), 'o');
    }

    /**
     * Get a wishlist by id, for a customer.
     *
     * @param CustomerInterface $customer
     * @param $id
     * @return WishlistInterface
     */
    public function findForCustomer(CustomerInterface $customer, $id)
    {
        // Get query builder
        $queryBuilder = $this->createQueryBuilder('o');

        // Apply customer condition
        $queryBuilder->where('IDENTITY(o.customer) = :customerId');
        $queryBuilder->setParameter('customerId', $customer->getId());

        // Apply wishlist ID parameter
        $queryBuilder->andWhere('o.id = :id');
        $queryBuilder->setParameter('id', $id);

        // Get the result
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Get the first wishlist for the customer, if any.
     *
     * @param CustomerInterface $customer
     * @return WishlistInterface|null
     */
    public function getFirstForCustomer(CustomerInterface $customer)
    {
        // Get query builder
        $queryBuilder = $this->createQueryBuilder('o');

        // Apply customer condition
        $queryBuilder->where('IDENTITY(o.customer) = :customerId');
        $queryBuilder->setParameter('customerId', $customer->getId());
        $queryBuilder->orderBy('o.createdAt', 'asc');
        $queryBuilder->setMaxResults(1);

        // Get the result
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
