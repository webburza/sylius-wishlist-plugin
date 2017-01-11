<?php

namespace Webburza\Sylius\WishlistBundle\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

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
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * @param UserInterface $user
     *
     * @return WishlistInterface
     */
    public function createDefault(UserInterface $user)
    {
        // Create a new wishlist
        $wishlist = $this->createNew();

        // Set default title
        $wishlist->setTitle($this->translator->trans('webburza_wishlist.wishlist.default_title'));

        // Set default public state
        $wishlist->setPublic($this->defaultPublic);

        // Set user
        $wishlist->setUser($user);

        return $wishlist;
    }
}
