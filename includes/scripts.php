<?php
/**
 * Scripts
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function edd_favorites_scripts() {
	
	// add validation to favorites page
	if ( edd_favorites_is_favorites() && edd_wl_sharing_is_enabled( 'email' ) ) {
		wp_enqueue_script( 'edd-wl-validate' );
	}

	// remove modal script
	if ( ! ( edd_wl_is_page( 'view' ) && edd_wl_sharing_is_enabled( 'email' ) ) ) {
		wp_dequeue_script( 'edd-wl-modal' );
	}

}
add_action( 'wp_enqueue_scripts', 'edd_favorites_scripts', 101 );

/**
 * Default plugin CSS
 * 
 * @return [type] [description]
 */
function edd_favorites_css() {
	?>
	<style>
		.favorited .glyphicon-heart { color: #D94D4D; }
		.favorited .glyphicon-star { color: #F2A711; }
		.favorited .glyphicon-gift { color: #41A3D0; }
		.favorited .glyphicon-bookmark { color: #D94D4D; }
		.favorited .glyphicon-add { color: #5FBC46; }
		.button.edd-wl-favorite { padding: 6px 8px; }
	</style>
<?php }
add_action( 'wp_head', 'edd_favorites_css' );

/**
 * JS
 */
function edd_favorites_js() {
?>
	<script>
	
	jQuery(document).ready(function ($) {
			
			$('body').on('click.eddFavorite', '.edd-wl-favorite', function (e) {
			    e.preventDefault();

			    var $spinner      	= $(this).find('.edd-loading');

			    var spinnerWidth    = $spinner.width(),
			    spinnerHeight       = $spinner.height();

			    // Show the spinner
			    $(this).attr('data-edd-loading', '');

			    // center spinner
			    $spinner.css({
			        'margin-left': spinnerWidth / -2,
			        'margin-top' : spinnerHeight / -2
			    });

			    var linkClicked = $(this);
				var container = $(this).closest('div');

				var form 			= $(this).closest('form'); // get the closest form element			
			    var form            = jQuery('.edd_download_purchase_form');
			    var download        = $(this).data('download-id');
			    var variable_price  = $(this).data('variable-price');
			    var price_mode      = $(this).data('price-mode');
			    var item_price_ids  = [];
			  	
			  	// we're only saving the download ID
				item_price_ids[0] = download;

			    var action          = $(this).data('action');

			    var data = {
			        action:     	action,
			        download_id:  	download,
			        price_ids:  	item_price_ids,
			        nonce:      	edd_scripts.ajax_nonce
			    };
			    
			    $.ajax({
			        type:       "POST",
			        data:       data,
			        dataType:   "json",
			        url:        edd_scripts.ajaxurl,
			        success: function (response) {
			            var cssClass = '<?php echo edd_favorites_css_class(); ?>';

			         	 $( linkClicked ).removeAttr( 'data-edd-loading' );

			           	if ( response.removed ) {
							$( linkClicked ).removeClass( cssClass );
			           	}
			           	else if ( response.added ) {
			           		$( linkClicked ).addClass( cssClass );
			           	}

			        }
			    })
			    .fail(function (response) {
			        console.log(response);
			    })
			    .done(function (response) {
			        console.log(response);
			    });

			    return false;
			});

		});
	</script>
<?php
}
add_action( 'wp_footer', 'edd_favorites_js', 100 );