<?php
/**
 * Labels
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Change the labels
*/
function edd_favorites_default_labels() {
	$defaults = array(
	   'singular' => __( 'Favorite', 'edd-favorites' ),
	   'plural' => __( 'Favorites', 'edd-favorites')
	);

	return apply_filters( 'edd_favorites_default_labels', $defaults );
}

/**
 * Messages
 * 
 * @since 1.0
 */
function edd_favorites_messages( $messages ) {
	if ( ! edd_favorites_is_favorites() ) {
		return $messages;
	}

	$new_messages = array(
		'list_updated'			=> sprintf( __( 'Successfully updated', 'edd-favorites' ), edd_wl_get_label_singular() ),
		'modal_share_title'		=> __( 'Share your favorites', 'edd-favorites' ),
	);

	$new_messages = apply_filters( 'edd_favorites_messages', $new_messages );

	return array_merge( $messages, $new_messages );
}
add_filter( 'edd_wl_messages', 'edd_favorites_messages' );