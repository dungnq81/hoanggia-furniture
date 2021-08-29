<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'W_Shortcode' ) ) {

	/**
	 * Class W_Shortcode
	 */
	class W_Shortcode {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * W_Shortcode constructor.
		 */
		public function __construct() {

			add_shortcode( 'safe_mail', [ $this, 'safe_mailto' ] );
			add_shortcode( 'footer_logo', [ $this, 'footer_logo' ] );
			add_shortcode( 'product_file', [ $this, 'product_file' ] );
			add_shortcode( 'product_gallery', [ $this, 'product_gallery' ] );
		}

		/**
		 * @param array $atts
		 */
		public function product_gallery( $atts = [] ) {
			$post = get_post();

			// normalize attribute keys, lowercase
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );

			// override default attributes
			$a = shortcode_atts( [
				'id' => $post->ID,
			], $atts );

			$galleries = get_field( 'gallery', $post->ID);
			$videos = get_field( 'video', $post->ID);
			if ($galleries || $videos) {
				?>
				<ul class="product_gallery_list">
					<?php foreach ($galleries as $gal) { if ($gal) { ?>
					<li class="item">
						<a title href="<?php echo image_src( $gal, 'post-thumbnail' );?>" data-fancybox="product-gallery__list-item">
							<?php echo wp_get_attachment_image( $gal, 'thumbnail');?>
						</a>
						<h6 class="file-name"><?php echo get_the_title($gal)?></h6>
						<p class="view-now">
							<a title href="<?php echo image_src( $gal, 'post-thumbnail');?>" data-fancybox="product-gallery__list-item-link">Xem ảnh ></a>
						</p>
					</li>
					<?php }} ?>
					<?php foreach ($videos as $vid) { if ($vid) { ?>
						<li class="item">
							<a title href="<?php echo $vid['video_link']?>" data-fancybox="product-gallery__list-item">
								<img src="<?php echo youtube_image($vid['video_link'])?>" alt>
							</a>
							<h6 class="file-name"><?php echo $vid['video_name']?></h6>
							<p class="view-now">
								<a title href="<?php echo $vid['video_link']?>" data-fancybox="product-gallery__list-item-link">Xem video ></a>
							</p>
						</li>
					<?php }}?>
				</ul>
				<?php
			}
		}

		/**
		 * @param array $atts
		 */
		public function product_file( $atts = [] ) {
			$post = get_post();

			// normalize attribute keys, lowercase
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );

			// override default attributes
			$a = shortcode_atts( [
				'id' => $post->ID,
			], $atts );

			$files = get_field( 'download_file', $post->ID);
			if ($files) {
				//var_dump($files);
			?>
			<ul class="product_file_list">
				<?php foreach ($files as $file) { ?>
				<li class="item">
					<i class="fal fa-file-pdf"></i>
					<h5 class="file-name"><?php echo $file['pdf_name']?></h5>
					<p class="download-now">
						<a class="_blank" download="<?php echo $file['pdf_name']?>" href="<?php echo $file['pdf_link']?>">Tải về ></a>
					</p>
				</li>
				<?php } ?>
			</ul>
		<?php
		}}

		/**
		 * @param array $atts
		 *
		 * @return string
		 */
		public function footer_logo( $atts = [] ) {

			// normalize attribute keys, lowercase
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );
			return footer_logo();
		}

		/**
		 * @param array $atts
		 */
		public function safe_mailto( $atts = [] ) {

			// normalize attribute keys, lowercase
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );

			// override default attributes
			$a = shortcode_atts( [
				'email' => 'info@webhd.vn',
				'title' => '',
				'attributes' => '',
				'class' => '',
				'id' => esc_attr( uniqid( 'mail-' ) )
			], $atts );

			$_attr = [];
			if ( $a['id'] ) {
				$_attr['id'] = $a['id'];
			}

			if ( $a['class'] ) {
				$_attr['class'] = $a['class'];
			}

			if (empty($a['title'])) {
				$a['title'] = esc_attr($a['email']);
			}

			$_attr['title'] = $a['title'];

			if ( $a['attributes'] ) {
				$a['attributes'] = array_merge ($_attr, (array) $a['attributes'] );
			}
			else {
				$a['attributes'] = $_attr;
			}

			return safe_mailto( $a['email'], $a['title'], $a['attributes'] );
		}
	}

	W_Shortcode::get_instance();
}
