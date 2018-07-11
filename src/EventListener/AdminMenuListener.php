<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\EventListener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addMenuItems(MenuBuilderEvent $event) : void
    {
        $menu = $event->getMenu();

        // Get or create the parent group
        if (null === ($customerMenu = $menu->getChild('customers'))) {
            $customerMenu = $menu->addChild('customers')->setLabel('sylius.ui.customer');
        }

        // Add 'Wishlists' menu item
        $customerMenu
            ->addChild('webburza_sylius_wishlist.wishlists', ['route' => 'webburza_sylius_wishlist_admin_wishlist_index'])
            ->setLabel('webburza_sylius_wishlist.ui.wishlists')
            ->setLabelAttribute('icon', 'star');
    }
}
