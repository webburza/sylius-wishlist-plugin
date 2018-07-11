@managing_wishlists_in_admin
Feature: Viewing a listing of wishlists in admin
  In order to have an overview of users wishlists
  As an Administrator
  I want to be able to view a listing of wishlists

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator
    And there is a customer account "first@example.com"
    And there is a customer account "second@example.com"
    And there are wishlists:
      | title              | customer           |
      | My first wishlist  | first@example.com  |
      | My second wishlist | second@example.com |

  @ui
  Scenario: Viewing a listing of wishlists
    Given I open wishlist index page
    Then I should see in the listing:
      | Title              | Customer           |
      | My first wishlist  | first@example.com  |
      | My second wishlist | second@example.com |
