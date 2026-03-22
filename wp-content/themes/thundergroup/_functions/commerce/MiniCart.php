<?php

use Timber\Timber;

/**
 * Mini Cart control (display product qty/amount in header)
 */

// Update cart fragments for AJAX add-to-cart
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    global $woocommerce;

    $context = ['cart' => $woocommerce->cart];

    $fragments['a.cart-mini-contents'] = Timber::compile(
        '_shop/cart-fragment-link.twig',
        $context
    );

    $fragments['div.cart-slideout-details'] = Timber::compile(
        '_shop/ajax-cart-content.twig',
        $context
    );

    return $fragments;
});
