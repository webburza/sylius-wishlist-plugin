<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Factory;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;

class WishlistFactory implements WishlistFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var bool
     */
    protected $defaultPublic;

    /**
     * @param FactoryInterface $factory
     * @param TranslatorInterface $translator
     * @param bool $defaultPublic
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        $defaultPublic
    ) {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->defaultPublic = $defaultPublic;
    }

    /**
     * @return WishlistInterface|object
     */
    public function createNew() : WishlistInterface
    {
        return $this->factory->createNew();
    }

    /**
     * @param ShopUserInterface $user
     *
     * @return WishlistInterface
     */
    public function createDefault(ShopUserInterface $user) : WishlistInterface
    {
        // Create a new wishlist
        $wishlist = $this->createNew();

        // Set default title
        $wishlist->setTitle($this->translator->trans('webburza_sylius_wishlist.ui.default_title'));

        // Set default public state
        $wishlist->setPublic($this->defaultPublic);

        // Set user
        $wishlist->setUser($user);

        return $wishlist;
    }
}
