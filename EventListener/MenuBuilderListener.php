<?php

namespace Webburza\Sylius\WishlistBundle\EventListener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class MenuBuilderListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addBackendMenuItems(MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();

        // Get or create the parent group
        if (null == ($contentMenu = $menu->getChild('customers'))) {
            $contentMenu = $menu->addChild('customers')->setLabel('webburza_wishlist.ui.customer');
        }

        // Add 'Wishlists' menu item
        $contentMenu->addChild('webburza_wishlists', ['route' => 'webburza_wishlist_admin_wishlist_index'])
                    ->setLabel('webburza_wishlist.ui.wishlists')
                    ->setLabelAttribute('icon', 'star');
    }
}
