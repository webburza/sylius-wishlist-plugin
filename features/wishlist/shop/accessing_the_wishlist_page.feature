@managing_wishlists_in_shop
Feature: Accessing the wishlist page
  In order to manage my wishlist
  As a logged user
  I want to be able to access my wishlist

  Background:
    Given the store operates on a single channel in "United States"
    And there is a customer account "first@example.com"
    And the store has "Lorem product", "Ipsum product" and "Dolor product" products
    And there are wishlists:
      | title              | customer           | item_count |
      | My first wishlist  | first@example.com  | 2          |

  @ui
  Scenario: Not being able to access the wishlist page as an anonymous user
    Given I open the wishlist page
    Then the response status code should be 404

  @ui
  Scenario: Being able to access the wishlist page as a logged-in user
    Given I am logged in as "first@example.com"
    And I open the wishlist page
    Then the response status code should be 200
    And I should see "My first wishlist"

  @ui
  Scenario: Viewing the listing of items on wishlist
    Given I am logged in as "first@example.com"
    And I open the wishlist page
    Then the response status code should be 200
    And I should see "My first wishlist"
    And I should see "Lorem product"
    And I should see "Ipsum product"
    And I should not see "Dolor product"
