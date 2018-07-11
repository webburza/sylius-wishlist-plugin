@managing_wishlists_in_shop
Feature: Adding items to wishlist
  In order to manage my wishlist
  As a logged user
  I want to be able to add items to it

  Background:
    Given the store operates on a single channel in "United States"
    And there is a customer account "first@example.com"
    And the store has "Lorem", "Ipsum", "Dolor" and "Sit" products

  @ui
  Scenario: Not viewing the add-to-wishlist button as an anonymous user
    Given I am on a product page
    Then I should not see "Add to wishlist" in the "#sylius-product-adding-to-cart" element

  @ui
  Scenario: Viewing the add-to-wishlist button as a logged-in user
    Given I am logged in as "first@example.com"
    And I am on a product page
    Then I should see "Add to wishlist" in the "#sylius-product-adding-to-cart" element

  @ui @javascript
  Scenario: Adding an item to wishlist
    Given I am logged in as "first@example.com"
    And I am on a product page
    And I press "Add to wishlist"
    And I wait "1" seconds
    Then the wishlist badge should indicate "1" products
