@managing_wishlists_in_admin
Feature: Viewing wishlist details in admin
  In order to have an overview of users wishlists
  As an Administrator
  I want to be able to view details of a single wishlist

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator
    And there is a customer account "first@example.com"
    And there are wishlists:
      | title              | customer           |
      | My first wishlist  | first@example.com  |

  @ui
  Scenario: Viewing details of a single wishlist
    Given I open a wishlist for customer account "first@example.com"
    Then I should see "My first wishlist" in the header
