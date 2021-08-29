<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$dir = __DIR__;

//require $dir . '/image/custom_image.php';

if ( ! is_woocommerce_activated() ) {
	require $dir . '/product/custom_product.php';
}
