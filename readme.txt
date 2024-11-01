 === Discontinued Stock Status For WooCommerce ===
Contributors: ncpenn
Tags: woocommerce, discontinued stock status
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: trunk
Requires PHP: 5.6.2
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Now you can keep products in your catalog even if they are permanently discontinued. Works for both simple and variable products. 

== Description ==

"Out of stock" implies a product that will again be in stock at some point. What if you have a product that will never be stocked again?

You could delete the product, and you could even do a 301 redirect for SEO purposes, but up till now, there was no good way to keep the product while marking it permanently out of stock.

With this plugin, this is now an option. The concept is from [WooCommerce Ideas Forum](http://ideas.woocommerce.com/forums/133476-woocommerce/suggestions/3778491-discontinued-products-option-under-inventory-sto)

The discontinued option is available in the stock status drop-down, and it works for either simple products and variable products.

== Installation ==

*WooCommerce is (obviously) required for this plugin*

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Navigate to an admin product page. Go to the "Product Data" meta box.
1. Select "Discontinued" from the stock status drop-down.

== Frequently Asked Questions ==

= What happens to discontinued products if I delete the plugin? =

Anything marked as discontinued will be reassigned to "out of stock."

= For variable products, can some variations be marked as discontinued and others non-discontinued statuses? =

Totally. Any number of variations can be marked as discontinued, and it will be reflected correctly on the public facing side of the store.
