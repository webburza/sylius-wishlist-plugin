@managing_wishlists_in_shop
Feature: Viewing the wishlist badge
  In order to access my wishlist and know the number of items it contains
  As a logged user
  I want to be able to see a badge link in the header

  Background:
    Given the store operates on a single channel in "United States"
    And there is a customer account "first@example.com"
    And the store has "Lorem", "Ipsum", "Dolor" and "Sit" products
    And there are wishlists:
      | title              | customer           | item_count |
      | My first wishlist  | first@example.com  | 3          |

  @ui
  Scenario: Not viewing the badge as an anonymous user
    Given I am on the homepage
    Then I should not see the wishlist badge in the header

  @ui
  Scenario: Viewing the badge as a logged-in user
    Given I am logged in as "first@example.com"
    And I am on the homepage
    Then I should see the wishlist badge in the header

  @ui
  Scenario: Viewing the number of items on wishlist badge as a logged-in user
    Given I am logged in as "first@example.com"
    And I am on the homepage
    Then I should see the wishlist badge in the header
    And the wishlist badge should indicate "3" products
