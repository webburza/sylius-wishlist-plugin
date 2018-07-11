<?php

namespace Tests\Webburza\SyliusWishlistPlugin\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;

class ShopContext extends MinkContext
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param RouterInterface $router
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        RouterInterface $router
    ) {
        $this->productRepository = $productRepository;
        $this->router = $router;
    }

    /**
     * @Then /^I should see the wishlist badge in the header$/
     */
    public function iShouldSeeTheWishlistBadgeInTheHeader()
    {
        $this->assertElementOnPage('#wishlistBadge');
    }

    /**
     * @Then /^I should not see the wishlist badge in the header$/
     */
    public function iShouldNotSeeTheWishlistBadgeInTheHeader()
    {
        $this->assertElementNotOnPage('#wishlistBadge');
    }

    /**
     * @Given /^the wishlist badge should indicate "([^"]*)" products$/
     */
    public function theWishlistBadgeShouldIndicateProducts($itemCount)
    {
        $this->assertElementContainsText('#wishlistBadge span', $itemCount);
    }

    /**
     * @Given /^I am on a product page$/
     */
    public function iAmOnAProductPage()
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);

        $this->visit($this->router->generate('sylius_shop_product_show', [
            'slug' => $product->getSlug(),
            '_locale' => 'en_US'
        ]));
    }

    /**
     * @Given /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($numberOfSeconds)
    {
        $this->getSession()->wait($numberOfSeconds * 1000);
    }

    /**
     * @Given /^I open the wishlist page$/
     */
    public function iOpenTheWishlistPage()
    {
        $this->visit($this->router->generate('webburza_sylius_wishlist_shop_wishlist_first'));
    }

    /**
     * @Given /^I click the wishlist badge$/
     */
    public function iClickTheWishlistBadge()
    {
        $this->clickLink('wishlistBadge');
    }

    /**
     * @Then /^I should be on the wishlist page$/
     */
    public function iShouldBeOnTheWishlistPage()
    {
        $this->assertPageAddress(
            $this->router->generate('webburza_sylius_wishlist_shop_wishlist_first')
        );
    }
}
