<?php
	$fb_appid  = get_theme_mod_ssl( 'fb_menu_layout' );
	$zalo_oaid = get_theme_mod_ssl( 'zalo_oa_menu_setting' );
	if ( $zalo_oaid || $fb_appid ) :
?>
<div class="inline-share">
	<label class="title">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M352 320c-25.6 0-48.9 10-66.1 26.4l-98.3-61.5c5.9-18.8 5.9-39.1 0-57.8l98.3-61.5C303.1 182 326.4 192 352 192c53 0 96-43 96-96S405 0 352 0s-96 43-96 96c0 9.8 1.5 19.6 4.4 28.9l-98.3 61.5C144.9 170 121.6 160 96 160c-53 0-96 43-96 96s43 96 96 96c25.6 0 48.9-10 66.1-26.4l98.3 61.5c-2.9 9.4-4.4 19.1-4.4 28.9 0 53 43 96 96 96s96-43 96-96-43-96-96-96zm0-272c26.5 0 48 21.5 48 48s-21.5 48-48 48-48-21.5-48-48 21.5-48 48-48zM96 304c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm256 160c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z"/></svg>
		<?php _e( 'Share', 'hd' )?>
	</label>
	<div class="inline-share-groups">
		<?php if ($fb_appid) : ?>
		<div class="fb-share-button" data-href="<?php echo current_url();?>" data-layout="button_count" data-size="small"></div>
		<?php endif; if ($zalo_oaid) : ?>
		<div class="zalo-share-button" data-href="" data-oaid="<?php echo $zalo_oaid;?>" data-layout="1" data-color="blue" data-customize=false></div>
		<div class="zalo-follow-only-button" data-oaid="<?php echo $zalo_oaid;?>"></div>
		<?php endif;?>
	</div>
</div>
<?php
	endif;
