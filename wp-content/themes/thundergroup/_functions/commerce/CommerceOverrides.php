<?php
/**
 * Healthy overrides for WooCommerce
 */

// Change the quantity & columns of related products
add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 1;
    return $args;
});

// Reorder product summary
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 21);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 29);


/**
 * WooCommerce + Timber Configuration
 */
function timber_set_product($post)
{
    global $product;

    if (is_woocommerce()) {
        $product = wc_get_product($post->ID);
    }
}