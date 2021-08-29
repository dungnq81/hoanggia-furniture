<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$dir = __DIR__;

// Show in WP Dashboard notice about the plugin is not activated.
require $dir . '/elementor/elementor-admin-notice.php';

require $dir . '/class-W_Fonts.php';
require $dir . '/class-W_ACF.php';
require $dir . '/class-W_ArContactUs.php';
require $dir . '/class-W_CF7.php';
require $dir . '/class-W_Jetpack.php';
require $dir . '/class-W_RankMath.php';
require $dir . '/class-W_Wpdiscuz.php';
