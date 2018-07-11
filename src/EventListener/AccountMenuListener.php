<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\EventListener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\Translation\TranslatorInterface;
use Webburza\SyliusWishlistPlugin\Provider\LoggedInUserProviderInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class AccountMenuListener
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LoggedInUserProviderInterface
     */
    protected $loggedInUserProvider;

    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var bool
     */
    protected $multipleWishlistMode;

    /**
     * FrontendMenuBuilderListener constructor.
     *
     * @param TranslatorInterface $translator
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param $multipleWishlistMode
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggedInUserProviderInterface $loggedInUserProvider,
        WishlistRepositoryInterface $wishlistRepository,
        $multipleWishlistMode
    ) {

        $this->translator = $translator;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->wishlistRepository = $wishlistRepository;
        $this->multipleWishlistMode = $multipleWishlistMode;
    }

    /**
     * Add menu items for the account section.
     *
     * @param MenuBuilderEvent $event
     */
    public function addMenuItems(MenuBuilderEvent $event) : void
    {
        // Get the menu
        $menu = $event->getMenu();

        // Set route and label, depending on multiple wishlist mode
        if ($this->multipleWishlistMode) {
            $route = 'webburza_sylius_wishlist_account_wishlist_index';
            $routeParameters = [];
            $label = $this->translator->trans('webburza_sylius_wishlist.ui.account_wishlists');
        } else {
            // Get the first wishlist for the user
            $wishlist = $this->wishlistRepository->getFirstForUser(
                $this->loggedInUserProvider->getUser()
            );

            if (!$wishlist) {
                return;
            }

            $route = 'webburza_sylius_wishlist_account_wishlist_edit';
            $routeParameters = [
                'id' => $wishlist->getId()
            ];
            $label = $this->translator->trans('webburza_sylius_wishlist.ui.account_wishlist');
        }

        // Add menu item to the menu
        $menu->addChild('wishlists', [
            'route' => $route,
            'routeParameters' => $routeParameters,
            'linkAttributes' => ['title' => $label],
            'labelAttributes' => ['icon' => 'star', 'iconOnly' => false],
        ])->setLabel($label);
    }
}
