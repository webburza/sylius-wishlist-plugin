# Sylius Wishlist Bundle

This bundle adds wishlist functionality to Sylius e-commerce platform. It can be configured
to use single or multiple wishlists per user, which can be public or private.

IMAGES

---

## Installation

  1. require the bundle with Composer:

  ```bash
  $ composer require webburza/sylius-wishlist-bundle
  ```

  2. enable the bundle in `app/AppKernel.php`:

  ```php
  public function registerBundles()
  {
    $bundles = array(
      // ...
      new \Webburza\Sylius\WishlistBundle\WebburzaSyliusWishlistBundle(),
      // ...
    );
  }
  ```

  3. add configuration to the top of `app/config/config.yml`:

  ```yaml
  imports:
      - { resource: @WebburzaSyliusWishlistBundle/Resources/config/config.yml }
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
  webburza_sylius_wishlist_bundle:
      resource: "@WebburzaSyliusWishlistBundle/Resources/config/routing.yml"

  webburza_sylius_wishlist_bundle_front:
      resource: "@WebburzaSyliusWishlistBundle/Resources/config/routingFront.yml"
      prefix: /wishlist

  webburza_sylius_wishlist_bundle_account:
      resource: "@WebburzaSyliusWishlistBundle/Resources/config/routingAccount.yml"
      prefix: /account/wishlists
  ```

  As you can see, there are three groups of routes, the main resource (administration)
  routes, frontend routes, and user account routes where the user can manage their
  wishlist(s), create new ones, mark them public/private, etc...

  5. The bundle should now be fully integrated, but it still requires
database tables to be created and RBAC permissions set. To ease this
process, after you've integrated the bundle you can run the
following command:

  ```bash
  $ app/console webburza:sylius-wishlist-bundle:install
  ```

  This will create all the required database tables, prefixed with `webburza_`,
and all the RBAC permissions, under the existing 'content' node.

  6. By default, users have no wishlists. When the user adds something to a wishlist
  for the first time, if they have no wishlists, a new wishlist will be created for them.
  There is a command available to create initial wishlists for all users, which we
  would recommend using.

  ```bash
  $ app/console webburza:sylius-wishlist-bundle:create-initial
  ```

## Integration on products pages

Now that you've installed and integrated the bundle, the users can view their wishlists,
create new ones, etc, depending on bundle configuration, but they still have no way of
adding products to wishlists. Since each project will have custom product pages,
this implementation is up to you. It can be done in two ways.

  1. Simple

  Since 'add to wishlist' functionality is almost the same as adding items to cart,
  the simplest way to finalize integration is to add a new 'Add to wishlist' button
  next to the 'Add to cart' button in the existing form.

  Open your product show.html.twig file, most likely in:
  app/Resources/SyliusWebBundle/views/Frontend/Product/show.html.twig

  Find the 'add to cart' button, by default:
  ```
  <button type="submit" class="btn btn-success btn-lg btn-block"><i class="icon-shopping-cart icon-white icon-large"></i> {{ 'sylius.add_to_cart'|trans }}</button>
  ```

  And under it, add the new 'add to wishlist' button.
  ```
  {% if app.user and app.user.customer %}
    <button type="submit" class="btn btn-success btn-block" formaction="{{ path('webburza_wishlist_frontend_add_item', {'id': product.id}) }}"><i class="icon-star icon-white"></i> {{ 'webburza.sylius.wishlist.add_to_wishlist'|trans }}</button>
  {% endif %}
  ```

  The main difference is the `formaction` attribute, which submits the same form
  to a different URL, in this case, the route which adds an item to wishlist.

  It is very important to note that this will not work for IE9 and older browsers.
  [Click here](http://www.w3schools.com/tags/att_button_formaction.asp) for details.

  Alternatively you can implement your own client-side logic to change the URL to which the
  form is submitted.

  This will add the product to the first wishlist found for the user, but if you're using
  the bundle in multiple wishlist mode, you'll want the user to be able to choose the
  appropriate wishlist. Add the following code above the 'add to wishlist' button, and
  style it however you want to match your template.

  ```
  {% if wishlist_provider.wishlists | length > 1 %}
      <label for="wishlist_id">{{ 'webburza.sylius.wishlist.frontend.wishlist' | trans }}</label>
      <select name="wishlist_id" id="wishlist_id">
          {% for wishlist in wishlist_provider.wishlists %}
              <option value="{{ wishlist.id }}">{{ wishlist.title }}</option>
          {% endfor %}
      </select>
  {% endif %}
  ```

  The dropdown will only be rendered if the user has more than one wishlist.

  2. Custom AJAX implementation

  An alternative is to implement your own, fully custom 'add to wishlist' functionality.
  To accomplish this, submit data to the `webburza_wishlist_frontend_add_item` route.

  ```
  $.ajax({
      url: '/wishlist/item/',
      type: 'POST',
      data: {
          product_variant_id: 123,
          wishlist_id: 456 // optional
      },
      success: // ...
  });
  ```

  You can also submit the data in the same format as in the first example
  (the 'add-to-cart' form), both examples use the same route, and both
  accept variant data to be resolved (first example), or an already resolved
  product_variant_id.

## Translations and naming

The bundle has multilingual support, and language files can be
overridden as with any other bundle, by creating translation files in the
`app/Resources/WebburzaSyliusWishlistBundle/translations` directory.

To get started, check the bundle's main language file in:
[Resources/translations/messages.en.yml](Resources/translations/messages.en.yml)

## License

This bundle is available under the [MIT license](LICENSE).

## To-do

- Tests
