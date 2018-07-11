@managing_wishlists_in_admin
Feature: Accessing wishlists via sidebar link
    In order to have an overview of users wishlists
    As an Administrator
    I want to be able to access them

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Viewing the sidebar link
        Given I open administration dashboard
        Then I should see "Wishlists" link in the sidebar "Customer" section

    @ui
    Scenario: Navigating via the sidebar link
        Given I open administration dashboard
        And I click the "Wishlists" link in the sidebar
        Then I should be on the wishlist index page
        And I should see "Manage wishlists" in the header
