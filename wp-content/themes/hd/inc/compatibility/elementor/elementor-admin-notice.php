<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! did_action( 'elementor/loaded' ) ) {
	add_action( 'admin_notices', 'elementor_fail_load_admin_notice' );
}

add_action( 'wp_ajax_elementor_set_admin_notice_viewed', 'ajax_elementor_set_admin_notice_viewed' );

/**
 * Set Admin Notice Viewed.
 *
 * @return void
 */
function ajax_elementor_set_admin_notice_viewed() {
	update_user_meta( get_current_user_id(), 'elementor_install_notice', 'true' );
	die;
}

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @return void
 */
function elementor_fail_load_admin_notice() {

	// Leave to Elementor Pro to manage this.
	if ( function_exists( 'elementor_pro_load_plugin' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	if ( 'true' === get_user_meta( get_current_user_id(), 'elementor_install_notice', true ) ) {
		return;
	}

	$plugin = 'elementor/elementor.php';
	$installed_plugins = get_plugins();
	$is_elementor_installed = isset( $installed_plugins[ $plugin ] );
	if ( $is_elementor_installed ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$message = __( 'HD theme is a lightweight starter theme designed to work perfectly with Elementor Page Builder plugin.', 'hd' );

		$button_text = __( 'Activate Elementor', 'hd' );
		$button_link = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$message = __( 'HD theme is a lightweight starter theme, based on Hello Elementor theme. We recommend you use it together with Elementor Page Builder plugin, they work perfectly together!', 'hd' );

		$button_text = __( 'Install Elementor', 'hd' );
		$button_link = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
	}
	?>
	<style>
		.notice.elementor-notice {
			border-left-color: #9b0a46 !important;
			padding: 20px;
		}
		.rtl .notice.elementor-notice {border-right-color: #9b0a46 !important;}
		.notice.elementor-notice .elementor-notice-inner {
			display: table;
			width: 100%;
		}
		.notice.elementor-notice .elementor-notice-inner .elementor-notice-icon,
		.notice.elementor-notice .elementor-notice-inner .elementor-notice-content,
		.notice.elementor-notice .elementor-notice-inner .elementor-install-now {
			display: table-cell;
			vertical-align: middle;
		}
		.notice.elementor-notice .elementor-notice-icon {
			color: #9b0a46;
			font-size: 50px;
			width: 50px;
		}
		.notice.elementor-notice .elementor-notice-content {padding: 0 20px;}
		.notice.elementor-notice p {
			padding: 0;
			margin: 0;
		}
		.notice.elementor-notice h3 {margin: 0 0 5px;}
		.notice.elementor-notice .elementor-install-now {text-align: center;}
		.notice.elementor-notice .elementor-install-now .elementor-install-button {
			padding: 5px 30px;
			height: auto;
			line-height: 20px;
			text-transform: capitalize;
		}
		.notice.elementor-notice .elementor-install-now .elementor-install-button i {padding-right: 5px;}
		.rtl .notice.elementor-notice .elementor-install-now .elementor-install-button i {
			padding-right: 0;
			padding-left: 5px;
		}
		.notice.elementor-notice .elementor-install-now .elementor-install-button:active {transform: translateY(1px);}
		@media (max-width: 782px) {
			.notice.elementor-notice {padding: 10px;}
			.notice.elementor-notice .elementor-notice-inner {display: block;}
			.notice.elementor-notice .elementor-notice-inner .elementor-notice-content {
				display: block;
				padding: 0;
			}
			.notice.elementor-notice .elementor-notice-inner .elementor-notice-icon, .notice.elementor-notice .hello-elementor-notice-inner .elementor-install-now {display: none;}
		}
	</style>
	<script>!function ($) {
			$(function () {
				$( 'div.notice.elementor-install-elementor' ).on( 'click', 'button.notice-dismiss', function( event ) {
					event.preventDefault();
					$.post( ajaxurl, {
						action: 'elementor_set_admin_notice_viewed'
					} );
				} );
			});
		}(jQuery);
	</script>
	<div class="notice updated is-dismissible elementor-notice elementor-install-elementor">
		<div class="elementor-notice-inner">
			<div class="elementor-notice-icon">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/elementor-logo.png' ); ?>" alt="Elementor Logo" />
			</div>

			<div class="elementor-notice-content">
				<h3><?php esc_html_e( 'Thanks for installing HD Theme!', 'hd' ); ?></h3>
				<div>
					<p><?php echo esc_html( $message ); ?></p>
					<a href="https://go.elementor.com/hello-theme-learn/" target="_blank"><?php esc_html_e( 'Learn more about Elementor', 'hd' ); ?></a>
				</div>
			</div>

			<div class="elementor-install-now">
				<a class="button button-primary elementor-install-button" href="<?php echo esc_attr( $button_link ); ?>"><i class="dashicons dashicons-download"></i><?php echo esc_html( $button_text ); ?></a>
			</div>
		</div>
	</div>
	<?php
}
