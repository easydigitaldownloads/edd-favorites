<?php
/**
 * Template functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Set the 'view' and 'edit' query var on the favorites page
 * 
 * @since  1.0
 * @todo set query var for each user, other 
 */
function edd_favorites_set_query_var() {
	if ( edd_favorites_is_favorites() ) {
		set_query_var( 'wl_view', edd_favorites_get_users_list_id() );
	}

	if ( edd_favorites_is_edit_page() ) {
		set_query_var( 'wl_edit', edd_favorites_get_users_list_id() );
	}
}
add_action( 'template_redirect', 'edd_favorites_set_query_var', 9 ); // runs just before edd_wl_process_form_requests() so it can pick up the correct query_var 

/**
 * Messages
 * @return [type] [description]
 */
function edd_favorites_set_messages() {
	// get array of messages
	$messages = edd_wl_messages();

	/**
	 * wish-list-view.php
	*/
	if ( edd_favorites_is_favorites() ) {

		$downloads = edd_wl_get_wish_list( edd_wl_get_list_id() );

		// list updated
		if ( isset( $_GET['list'] ) && $_GET['list'] == 'updated' ) {
			edd_wl_set_message( 'list_updated', $messages['list_updated'] );
		}

		// no downloads
		if ( empty( $downloads ) ) {
			edd_wl_set_message( 'no_downloads', $messages['no_downloads'] );
		}
	}

}
add_action( 'template_redirect', 'edd_favorites_set_messages' );


/**
 * When a guest registers, copy their favorites list ID to their user profile
 */
function edd_favorites_new_user_registration( $user_id ) {
	// get user's list_id
	$list_id = edd_favorites_get_users_list_id();

	// copy this to the user meta
	if ( $list_id ) {
		update_user_meta( $user_id, 'edd_favorites_list_id', $list_id );
	}
}
add_action( 'user_register', 'edd_favorites_new_user_registration', 10, 1 );
add_action( 'wpmu_new_user', 'edd_favorites_new_user_registration', 10, 1 );


/**
 * Handles loading of the favorites link
 * @param  int $download_id download ID
 * @return void
 * @since  1.0
 */
function edd_favorites_load_link( $download_id = '' ) {
	global $edd_options;

	// don't show links if guests are not allowed to favorite
	if ( ! edd_wl_allow_guest_creation() )
		return;
	?>

	<?php
	// set the $download_id to the post ID if $download_id is not present
	if ( ! $download_id ) {
		$download_id = get_the_ID();
	}

	$classes = array();
	// assign a class to the link depending on where it's hooked to
	// this way we can control the margin needed at the top or bottom of the link
	if( has_action( 'edd_purchase_link_end', 'edd_favorites_load_link' ) ) {
		$classes[] = 'after';
	}
	elseif( has_action( 'edd_purchase_link_top', 'edd_favorites_load_link' ) ) {
		$classes[] = 'before';
	}

	// default classes
	$classes[] = 'edd-wl-action';
	$classes[] = 'edd-wl-favorite';

	$list_id = edd_favorites_get_users_list_id();

	// only add the favorites CSS class if the list exists
	if ( $list_id )
		$classes[] = edd_wl_item_in_wish_list( $download_id, null, $list_id ) ? edd_favorites_css_class() : '';

	
	// add $options
	$args = array(
		'download_id'	=> $download_id,
		'action'		=> 'edd_favorites_favorite',
		'class'			=> implode( ' ', $classes ),
		'link_size'		=> apply_filters( 'edd_wl_link_size', '' ),
		'text'        	=> ! empty( $edd_options[ 'edd_favorites_favorite' ] ) ? $edd_options[ 'edd_favorites_favorite' ] : '',
	);

	$args = apply_filters( 'edd_favorites_link', $args );

	edd_wl_wish_list_link( $args );
}

/**
 * Remove standard wish list links
 * @return [type] [description]
 * @uses  edd_favorites_load_link()
 */
function edd_favorites_link() {
	// remove standard add to wish list link
	remove_action( 'edd_purchase_link_top', 'edd_wl_load_wish_list_link' );

	// add our new link
	add_action( 'edd_purchase_link_top', 'edd_favorites_load_link' );
}
add_action( 'template_redirect', 'edd_favorites_link' );