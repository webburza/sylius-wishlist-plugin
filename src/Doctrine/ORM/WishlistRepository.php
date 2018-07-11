<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    /*
     * @return QueryBuilder
     */
    public function createListQueryBuilder() : QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->innerJoin('o.user', 'user');
        $queryBuilder->innerJoin('user.customer', 'customer');

        return $queryBuilder;
    }

    /**
     * @param ShopUserInterface $user
     *
     * @return int
     */
    public function getCountForUser(ShopUserInterface $user): int
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
     * @param ShopUserInterface $user
     *
     * @return WishlistInterface|object|null
     */
    public function getFirstForUser(ShopUserInterface $user): ?WishlistInterface
    {
        return $this->findOneBy([
            'user' => $user
        ], [
            'createdAt' => 'asc'
        ]);
    }
}
