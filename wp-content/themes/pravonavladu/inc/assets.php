<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 02.09.14
 * Time: 1:06
 */

function pnv_scripts() {
    wp_enqueue_style( 'pnv-fonts', get_template_directory_uri() . '/css/fonts/fonts.css');
    wp_enqueue_style( 'pnv-normalize', get_template_directory_uri() . '/css/normalize.min.css');
    //wp_enqueue_style( 'pnv-sidr', get_template_directory_uri() . '/css/jquery.sidr.dark.css');
    wp_enqueue_style( 'pnv-scrollbar', get_template_directory_uri() . '/css/jquery.mCustomScrollbar.css');

    wp_enqueue_style( 'pnv-style', get_stylesheet_uri(), array('pnv-normalize', 'pnv-fonts'), 2 );

    wp_enqueue_script( 'pnv-modernizr', get_template_directory_uri() . '/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js');
    //wp_enqueue_script( 'pnv-sidr', get_template_directory_uri() . '/js/vendor/jquery.sidr.min.js', array('jquery'));
    wp_enqueue_script( 'pnv-wheel', get_template_directory_uri() . '/js/vendor/jquery.mousewheel.min.js', array('jquery'));
    wp_enqueue_script( 'pnv-cookie', get_template_directory_uri() . '/js/vendor/jquery.cookie.js', array('jquery'));
    wp_enqueue_script( 'pnv-scrollbar', get_template_directory_uri() . '/js/vendor/jquery.mCustomScrollbar.min.js', array('jquery', 'pnv-wheel'));
    wp_enqueue_script( 'pnv-skrollr', get_template_directory_uri() . '/js/vendor/skrollr.js', array('jquery'), '', true );
    wp_enqueue_script( 'pnv-masonry', get_template_directory_uri() . '/js/vendor/masonry.pkgd.min.js', array('jquery'), '', true );
    wp_enqueue_script( 'pnv-imagesloaded', get_template_directory_uri() . '/js/vendor/imagesloaded.pkgd.min.js', array('jquery'), '', true );
    wp_enqueue_script( 'pnv-youtube', 'http://www.youtube.com/iframe_api', array('jquery'), '', true );
    wp_enqueue_script( 'pnv-swfobject', get_template_directory_uri() . '/js/vendor/swfobject.js', array('jquery'), '', true );
    wp_enqueue_script( 'pnv-player', get_template_directory_uri() . '/js/player.js', array('pnv-swfobject'), '', true );
    wp_enqueue_script( 'pnv-main', get_template_directory_uri() . '/js/main.js', array('jquery', 'pnv-skrollr', 'pnv-youtube'), '2', true );

}
add_action( 'wp_enqueue_scripts', 'pnv_scripts' );


add_action('wp_head','add_global_js_vars');

function add_global_js_vars() {
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	<?php
}

add_action( 'admin_init', function() {
    wp_enqueue_style( 'pnv-admin', get_template_directory_uri() . '/css/admin.css');

} );