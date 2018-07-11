<?php

namespace Tests\Webburza\SyliusWishlistPlugin\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistItemInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Webburza\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Behat\Behat\Context\Context;

class WishlistContext implements Context
{
    /**
     * @var WishlistFactoryInterface
     */
    protected $wishlistFactory;

    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var UserRepositoryInterface
     */
    protected $shopUserRepository;

    /**
     * @var ProductVariantRepositoryInterface
     */
    protected $productVariantRepository;

    /**
     * @var FactoryInterface
     */
    protected $wishlistItemFactory;

    /**
     * @param WishlistFactoryInterface $wishlistFactory
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param UserRepositoryInterface $shopUserRepository
     * @param ProductVariantRepositoryInterface $productVariantRepository
     * @param FactoryInterface $wishlistItemFactory
     */
    public function __construct(
        WishlistFactoryInterface $wishlistFactory,
        WishlistRepositoryInterface $wishlistRepository,
        UserRepositoryInterface $shopUserRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        FactoryInterface $wishlistItemFactory
    ) {
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->shopUserRepository = $shopUserRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistItemFactory = $wishlistItemFactory;
    }

    /**
     * @Given /^there are wishlists:$/
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function thereAreWishlists(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $user = $this->shopUserRepository->findOneByEmail($row['customer']);

            if (!$user) {
                throw new \Exception("User '{$row['customer']}' not found.");
            }

            $wishlist = $this->wishlistFactory->createDefault($user);

            if (isset($row['title'])) {
                $wishlist->setTitle($row['title']);
            }

            if (isset($row['item_count'])) {
                $variants = $this->productVariantRepository->findBy([], null, $row['item_count']);

                foreach ($variants as $variant) {
                    /** @var WishlistItemInterface $wishlistItem */
                    $wishlistItem = $this->wishlistItemFactory->createNew();
                    $wishlistItem->setProductVariant($variant);

                    $wishlist->addItem($wishlistItem);
                }
            }

            $this->wishlistRepository->add($wishlist);
        }
    }
}
