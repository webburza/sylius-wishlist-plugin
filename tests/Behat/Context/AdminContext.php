<?php

namespace Tests\Webburza\SyliusWishlistPlugin\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Webmozart\Assert\Assert;

class AdminContext extends MinkContext
{
    /**
     * @var UserRepositoryInterface
     */
    protected $shopUserRepository;

    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param UserRepositoryInterface $shopUserRepository
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param RouterInterface $router
     */
    public function __construct(
        UserRepositoryInterface $shopUserRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RouterInterface $router
    ) {
        $this->shopUserRepository = $shopUserRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->router = $router;
    }

    /**
     * @Then /^I should see "([^"]*)" link in the sidebar "([^"]*)" section$/
     */
    public function iShouldSeeLinkInTheSidebarSection($linkLabel, $parentLabel)
    {
        $this->assertElementOnPage("#sidebar div.header:contains('$parentLabel') ~ div.menu a.item:contains('$linkLabel')");
    }

    /**
     * @Given I click the ":linkLabel" link in the sidebar
     */
    public function iClickTheLinkInTheSidebar($linkLabel)
    {
        $this->getSession()
             ->getPage()
             ->find('css', "#sidebar div.menu a.item:contains('$linkLabel')")
             ->click();
    }

    /**
     * @Then /^I should be on the wishlist index page$/
     */
    public function iShouldBeOnTheWishlistIndexPage()
    {
        $this->assertPageAddress(
            $this->router->generate('webburza_sylius_wishlist_admin_wishlist_index')
        );
    }

    /**
     * @Given /^I should see "([^"]*)" in the header$/
     */
    public function iShouldSeeInTheHeader($string)
    {
        $this->assertElementOnPage("#content h1.header:contains('$string')");
    }

    /**
     * @Given /^I open wishlist index page$/
     */
    public function iOpenWishlistIndexPage()
    {
        $this->visit(
            $this->router->generate('webburza_sylius_wishlist_admin_wishlist_index')
        );
    }

    /**
     * @Then /^I should see in the listing:$/
     */
    public function iShouldSeeInTheListing(TableNode $table)
    {
        $listing = $this->getSession()
                        ->getPage()
                        ->find('css', "#content table.ui.sortable");

        $listingData = $this->htmlTableToArray($listing);

        for ($i = 0; $i < count($table->getHash()); $i++) {
            foreach ($table->getHash()[$i] as $key => $value) {
                Assert::same($listingData[$i][$key],  $table->getHash()[$i][$key]);
            }
        }
    }

    /**
     * @Given /^I open a wishlist for customer account "([^"]*)"$/
     */
    public function iOpenAWishlistForCustomerAccount($customerEmail)
    {
        $user = $this->shopUserRepository->findOneByEmail($customerEmail);

        if (!$user) {
            throw new \Exception("User '$customerEmail' not found.");
        }

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneBy([
            'user' => $user
        ]);

        $this->visit($this->router->generate('webburza_sylius_wishlist_admin_wishlist_show', [
            'id' => $wishlist->getId()
        ]));
    }

    /**
     * @param NodeElement $htmlTable
     *
     * @return array
     */
    protected function htmlTableToArray(NodeElement $htmlTable)
    {
        $array = [];

        for ($j = 1; $j <= count($htmlTable->findAll('css', 'tbody tr')); $j++) {
            $row = [];

            for ($i = 1; $i <= count ($htmlTable->findAll('css', 'thead th')); $i++) {
                $key = $htmlTable->find('css', "thead th:nth-child($i)")->getText();
                $value = $htmlTable->find('css', "tbody tr:nth-child($j) td:nth-child($i)")->getText();

                $row[$key] = $value;
            }

            $array[] = $row;
        }

        return $array;
    }
}
