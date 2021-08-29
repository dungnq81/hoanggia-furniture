<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// classes
foreach ( glob( __DIR__ . "/classes/class-*.php" ) as $filename ) {
	if ( is_file( $filename ) ) {
		require_once $filename;
	}
}

// global widget
foreach ( glob( __DIR__ . "/widgets/widget_*.php" ) as $filename ) {
	if ( is_file( $filename ) ) {
		require_once $filename;
	}
}
