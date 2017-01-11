<?php

namespace Webburza\Sylius\WishlistBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\User\Model\UserInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Repository\WishlistRepositoryInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder()
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->innerJoin('o.user', 'user');

        return $queryBuilder;
    }

    /**
     * @param UserInterface $user
     *
     * @return integer
     */
    public function getCountForUser(UserInterface $user)
    {
        // Get query builder
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->select('COUNT(o)');

        // Apply user condition
        $queryBuilder->where('o.user = :user');
        $queryBuilder->setParameter('user', $user);

        // Return record count
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Get the first wishlist for the user, if any.
     *
     * @param UserInterface $user
     *
     * @return WishlistInterface|object|null
     */
    public function getFirstForUser(UserInterface $user)
    {
        return $this->findOneBy([
            'user' => $user
        ], [
            'createdAt' => 'asc'
        ]);
    }
}
