<?php
namespace Webburza\Sylius\WishlistBundle\EventListener;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Translator;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistRepositoryInterface;

class FrontendMenuBuilderListener
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     *@var SecurityContextInterface
     */
    protected $securityContext;

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
     * @param DataCollectorTranslator $translator
     * @param SecurityContextInterface $securityContext
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param $multipleWishlistMode
     */
    public function __construct(
        DataCollectorTranslator $translator,
        SecurityContextInterface $securityContext,
        WishlistRepositoryInterface $wishlistRepository,
        $multipleWishlistMode
    ) {
        $this->translator = $translator;
        $this->securityContext = $securityContext;
        $this->wishlistRepository = $wishlistRepository;
        $this->multipleWishlistMode = $multipleWishlistMode;
    }

    /**
     * Add wishlist(s) menu item, only if the current user has
     *
     * @param MenuBuilderEvent $event
     */
    public function addFrontendMenuItems(MenuBuilderEvent $event)
    {
        // Get the menu
        $menu = $event->getMenu();

        // Get the current customer
        $customer = $this->getCustomer();

        if (!$customer) {
            return;
        }

        // Get wishlist count
        $wishlistCount = $this->wishlistRepository->getCountForCustomer($customer);

        // If there's more than one wishlist,
        // add a link to the wishlist listing in the user's account
        if ($this->multipleWishlistMode && $wishlistCount > 1) {
            $this->addMenuItemForWishlists($menu);
        } elseif ($wishlistCount) {
            // Get the wishlist
            $wishlist = $this->wishlistRepository->getFirstForCustomer($customer);

            // Add the link, only if the wishlist has items
            if ($wishlist && $wishlist->getItems()->count()) {
                $this->addMenuItemForWishlist($menu, $wishlist);
            }
        }
    }

    /**
     * Add menu items for the account section.
     *
     * @param MenuBuilderEvent $event
     */
    public function addAccountMenuItems(MenuBuilderEvent $event)
    {
        // Get the menu
        $menu = $event->getMenu();

        // Set route and label, depending on multiple wishlist mode
        if ($this->multipleWishlistMode) {
            $route = 'webburza_wishlist_account_index';
            $routeParameters = [];
            $label = $this->translate('webburza.sylius.wishlist.frontend.my_wishlists');
        }
        else {
            // Get the current customer
            $customer = $this->getCustomer();

            if (!$customer) {
                return;
            }

            // Get the first wishlist for the user
            $wishlist = $this->wishlistRepository->getFirstForCustomer($customer);

            if (!$wishlist) {
                return;
            }

            $route = 'webburza_wishlist_account_edit';
            $routeParameters = [
                'id' => $wishlist->getId()
            ];
            $label = $this->translate('webburza.sylius.wishlist.frontend.my_wishlist');
        }

        // Add menu item to the menu
        $menu->addChild('wishlists', [
            'route' => $route,
            'routeParameters' => $routeParameters,
            'linkAttributes' => ['title' => $label],
            'labelAttributes' => ['icon' => 'icon-star', 'iconOnly' => false],
        ])->setLabel($label);
    }

    /**
     * @param ItemInterface $menu
     */
    protected function addMenuItemForWishlists(ItemInterface $menu)
    {
        $label = $this->translate('webburza.sylius.wishlist.frontend.my_wishlists');

        // Add menu item to the menu
        $menu->addChild('wishlists', [
            'route' => 'webburza_wishlist_account_index',
            'linkAttributes' => ['title' => $label],
            'labelAttributes' => ['icon' => 'icon-star', 'iconOnly' => false],
        ])->setLabel($label);
    }

    /**
     * Add a menu item for a wishlist.
     *
     * @param ItemInterface $menu
     * @param WishlistInterface $wishlist
     */
    protected function addMenuItemForWishlist(ItemInterface $menu, WishlistInterface $wishlist)
    {
        // Use general route (/wishlist)
        $route = 'webburza_wishlist_frontend_first';
        $routeParameters = [];

        // Route for a specific wishlist
        if ($this->multipleWishlistMode) {
            $route = 'webburza_wishlist_frontend_show';
            $routeParameters = [
                'slug' => $wishlist->getSlug()
            ];
        }

        $menu->addChild('webburza_sylius_wishlist_front', [
            'route' => $route,
            'routeParameters' => $routeParameters,
            'linkAttributes' => [
                'title' => $this->translate('webburza.sylius.wishlist.frontend.wishlist')
            ],
            'labelAttributes' => [
                'icon' => 'star',
                'iconOnly' => false
            ],
        ])->setLabel($this->translate('webburza.sylius.wishlist.frontend.wishlist'));
    }

    /**
     * Get the customer from the currently active user, if any.
     *
     * @return CustomerInterface|null
     */
    private function getCustomer()
    {
        if ($this->securityContext->getToken()) {
            $user = $this->securityContext->getToken()->getUser();

            if ($user instanceof UserInterface) {
                return $user->getCustomer();
            }
        }

        return null;
    }

    /**
     * Translate a string using the translator.
     *
     * @param $string
     * @return string
     */
    private function translate($string)
    {
        return $this->translator->trans($string);
    }
}
