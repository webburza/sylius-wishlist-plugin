# Sylius Wishlist Plugin

This bundle adds wishlist functionality to Sylius e-commerce platform. It can be configured
to use single or multiple wishlists per user, which can be public or private.

---

## Installation

  1. require the bundle with Composer:

  ```bash
  $ composer require webburza/sylius-wishlist-plugin
  ```

  2. enable the bundle in `app/AppKernel.php`:

  ```php
  public function registerBundles()
  {
    $bundles = [
      // ...
      new \Webburza\SyliusWishlistPlugin\WebburzaSyliusWishlistPlugin(),
      // ...
    ];
  }
  ```

  3. add configuration to the top of `app/config/config.yml`:

  ```yaml
  imports:
      - { resource: "@WebburzaSyliusWishlistPlugin/Resources/config/config.yml" }
  ```

  Among other things, this provides configuration entries which can then be overriden
  in your app's config.yml.

  ```
  webburza_sylius_wishlist:
      multiple: true           # multiple wishlist mode
      default_public: false    # used for automatically created wishlists
  ```

  4. register routes in `app/config/routing.yml`

  ```yaml
  webburza_sylius_wishlist:
    resource: "@WebburzaSyliusWishlistPlugin/Resources/config/routing.yml"
  ```

  5. The bundle should now be fully integrated, but it still requires
  database tables to be created. For this, we recommend using migrations.
  
  ```bash
  $ bin/console doctrine:migrations:diff
  $ bin/console doctrine:migrations:migrate
  ```
  
  Or if you don't use migrations, you can update the database schema directly.
  
  ```bash
    $ bin/console doctrine:schema:update
  ```

  6. If you're integrating this bundle into an existing project, your existing
  users will not have any wishlists associated. This is not an issue as wishlists
  are automatically created when needed.

## Integration on shop pages

Now that you've installed and integrated the bundle, the users can view their wishlists,
create new ones, etc, depending on bundle configuration, but they still have no way of
adding products to wishlists. Since each project will have custom product pages,
this implementation is up to you. It can be done in two ways.

  1. Simple

  Since 'add to wishlist' functionality is almost the same as adding items to cart,
  the simplest way to finalize integration is to add a new 'Add to wishlist' button
  next to the 'Add to cart' button in the existing form.

  Open the template containing your 'add to cart' form, most likely in:
  `app/Resources/SyliusShopBundle/views/Product/Show/_addToCart.html.twig`

  Find the 'add to cart' button, by default:
  ```
  <button type="submit" class="ui huge primary icon labeled button"><i class="cart icon"></i> {{ 'sylius.ui.add_to_cart'|trans }}</button>
  ```

  And under it, add the following line.
  ```
  {% include '@WebburzaSyliusWishlistPlugin/Shop/Misc/_addToWishlist.html.twig' %}
  ```
  
  This will include the 'Add to Wishlist' button, and all required functionality.
  It will also feature a dropdown if the user has more than one wishlist,
  to enable the user to select which wishlist they want to add the item to.

  The dropdown will only be rendered if the user has more than one wishlist.

  2. Custom AJAX implementation

  An alternative is to implement your own, fully custom 'add to wishlist' functionality.
  To accomplish this, submit data to the `webburza_wishlist_frontend_add_item` route.

  ```
  $.ajax({
      url: '/wishlist/item',
      type: 'POST',
      data: {
          productVariantId: 123,
          wishlistId: 456 // optional
      },
      success: // ...
  });
  ```

  You can also submit the data in the same format as in the first example
  (the 'add-to-cart' form), both examples use the same route, and both
  accept variant data to be resolved (first example), or an already resolved
  productVariantId.
  
### Wishlist badge

You might also want to feature a badge in your header which links to the wishlist
  and shows the current number of items added, similar to the existing cart badge.
  
To do this, just add this line to the bottom of the same file
`app/Resources/SyliusShopBundle/views/Cart/_widget.html.twig`

```
{% include '@WebburzaSyliusWishlistPlugin/Shop/Misc/_badge.html.twig' %}
```
  
## Translations and naming

The bundle has multilingual support, and language files can be
overridden as with any other bundle, by creating translation files in the
`app/Resources/WebburzaSyliusWishlistPlugin/translations` directory.

To get started, check the bundle's main language file in:
[src/Resources/translations/messages.en.yml](src/Resources/translations/messages.en.yml)

## Running and testing the application manually

- Initial installation and fixtures:

    ```bash
    $ composer install
    
    $ (cd tests/Application && yarn install)
    $ (cd tests/Application && yarn run gulp)
    $ (cd tests/Application && bin/console assets:install web -e dev)
    
    $ (cd tests/Application && bin/console doctrine:database:create -e dev)
    $ (cd tests/Application && bin/console doctrine:schema:create -e dev)
    $ (cd tests/Application && bin/console sylius:fixtures:load -e dev)
    ```
    
- Start application:

    ```bash
    $ (cd tests/Application && bin/console server:start -d web -e dev)
    ```
    
- Stop application:

    ```bash
    $ (cd tests/Application && bin/console server:stop)
    ```

## Automated tests

  - Behat (non-Javascript scenarios)

    ```bash
    $ bin/behat --tags="~@javascript"
    ```

  - Behat (with Javascript scenarios)
 
    1. Download [Chromedriver](https://sites.google.com/a/chromium.org/chromedriver/)
    
    2. Run Selenium server with previously downloaded Chromedriver:
    
        ```bash
        $ bin/selenium-server-standalone -Dwebdriver.chrome.driver=/path/to/chromedriver
        ```
    3. Run test application's webserver on `localhost:8080`:
    
        ```bash
        $ (cd tests/Application && bin/console server:run 127.0.0.1:8080 -d web -e test)
        ```
    
    4. Run Behat:
    
        ```bash
        $ bin/behat
        ```

## License

This bundle is available under the [MIT license](LICENSE).
