@managing_wishlists_in_shop
Feature: Navigating to the wishlist page
  In order to manage my wishlist
  As a logged user
  I want to be able to navigate to my wishlist

  Background:
    Given the store operates on a single channel in "United States"
    And there is a customer account "first@example.com"
    And I am logged in as "first@example.com"
    And there are wishlists:
      | title              | customer          |
      | My first wishlist  | first@example.com |

  @ui
  Scenario: Navigating to the first wishlist if there is only one
    Given I am on the homepage
    And I click the wishlist badge
    Then I should be on the wishlist page
