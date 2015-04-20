<?php
/**
 * Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Singular Label
 *
 * @since 1.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function edd_favorites_get_label_singular( $lowercase = false ) {
	$defaults = edd_favorites_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0
 * @return string $defaults['plural'] Plural label
 */
function edd_favorites_get_label_plural( $lowercase = false ) {
	$defaults = edd_favorites_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}


/**
 * Returns a user's favorites list ID
 * list will be created if one does not exist
 *
 * @since  1.0
 * @return [type] [description]
 * @uses edd_favorites_create_list_id()
 */
function edd_favorites_get_users_list_id() {

	// user is logged in, get ID of list
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();

		$list = get_user_meta( $user_id, 'edd_favorites_list_id', true );

		if ( 'publish' == get_post_status( $list ) || 'private' == get_post_status( $list ) ) {
			$list_id = $list;
		}
	}
	// user is logged out
	elseif ( ! is_user_logged_in() ) {

		// if user does not have token, exit
		if ( ! edd_wl_get_list_token() )
			return null;

		// find the list ID that has the 'edd_favorites_list_id' meta key and value of user's token
		$args = array(
			'post_type' 		=> 'edd_wish_list',
			'posts_per_page' 	=> '-1',
			'post_status' 		=> array( 'publish', 'private' ),
			'meta_key'			=> 'edd_favorites_list_id',
			'meta_value'		=> edd_wl_get_list_token()
		);

		$post = get_posts ( $args );
		$list_id = $post ? $post[0]->ID : '';
	}

	$list_id = isset( $list_id ) ? $list_id : '';

	return $list_id;
}

/**
 * Create a favorites list
 * @return [type] [description]
 */
function edd_favorites_create_list_id() {
	$args = array(
		'post_title'    => apply_filters( 'edd_favorites_post_title', __( 'My Favorites', 'edd-favorites' ) ),
		'post_content'  => '',
		'post_status'   => apply_filters( 'edd_favorites_post_status', 'private' ),
		'post_type'     => 'edd_wish_list',
	);

	$list_id = wp_insert_post( $args );

	if ( $list_id )
		return $list_id;
}

/**
 * Get the URI for viewing the favorites
 * @return string
 */
function edd_favorites_get_view_uri( $id = '' ) {
	global $edd_options;

	$uri = isset( $edd_options['edd_favorites_page_view'] ) ? get_permalink( $edd_options['edd_favorites_page_view'] ) : false;

	if ( edd_wl_has_pretty_permalinks() ) {
		$url = trailingslashit( $uri );
	}
	else {
		$url = add_query_arg( 'wl_view', $id, $uri );
	}

	return esc_url( apply_filters( 'edd_favorites_get_view_uri', $url ) );
}

/**
 * Get the URI for editing the favorites
 * @return string
 */
function edd_favorites_get_edit_uri( $id = '' ) {
	global $edd_options;

	$uri = isset( $edd_options['edd_favorites_page_edit'] ) ? get_permalink( $edd_options['edd_favorites_page_edit'] ) : false;

	if ( edd_wl_has_pretty_permalinks() ) {
		$url = trailingslashit( $uri );
	}
	else {
		$url = add_query_arg( 'wl_edit', $id, $uri );
	}

	return esc_url( apply_filters( 'edd_favorites_get_edit_uri', $url ) );
}


/**
 * Get the page ID of the favorites page
 * @return [type] [description]
 */
function edd_favorites_page_id() {
	$id = edd_get_option( 'edd_favorites_page_view', '' );

	if ( $id )
		return $id;

	return null;
}

/**
 * Is favorites page
 */
function edd_favorites_is_favorites() {
	$id = edd_get_option( 'edd_favorites_page_view' );

	if ( ! $id )
		return false;

	if ( is_page( $id ) )
		return true;

	return false;
}

/**
 * Is favorites edit page
 * @return [type] [description]
 */
function edd_favorites_is_edit_page() {
	$id = edd_get_option( 'edd_favorites_page_edit' );

	if ( is_page( $id ) )
		return true;

	return false;
}

/**
 * CSS class that's applied
 * @return string Name of the CSS class
 */
function edd_favorites_css_class() {
	return apply_filters( 'edd_favorites_css_class', 'favorited' );
}
