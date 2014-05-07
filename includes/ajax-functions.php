<?php
/**
 * Ajax functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds item to new list via Ajax
 *
 * @since 1.0
 * @return void
 */
function edd_favorites_favorite() {

	if ( isset( $_POST['download_id'] ) ) {
		global $post;

		$to_add = array();

		if ( isset( $_POST['price_ids'] ) && is_array( $_POST['price_ids'] ) ) {
			foreach ( $_POST['price_ids'] as $price ) {
				$to_add[] = array( 'price_id' => $price );
			}
		}

		$return = array();

		// get user's list ID if already exists
		$list_id = edd_favorites_get_users_list_id();

		// no list
		if ( ! $list_id ) {
			// create list if it does not exist
			$list_id = edd_favorites_create_list_id();

			// create token for logged out user
			$token = edd_wl_create_token( $list_id );

			// update list ID with token
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				// store list ID against user's profile
				update_user_meta( $user_id, 'edd_favorites_list_id', $list_id );
			} 
			elseif ( ! is_user_logged_in() ) {
				update_post_meta( $list_id, 'edd_favorites_list_id', $token );
			}
		}

		// add each download to wish list
		foreach ( $to_add as $options ) {
			if( $_POST['download_id'] == $options['price_id'] ) {
				$options = array();
			}

			// item already in list, remove
			if ( edd_wl_item_in_wish_list( $_POST['download_id'], $options, $list_id ) ) {
				$position = edd_wl_get_item_position_in_list( $_POST['download_id'], $list_id, $options );

				edd_remove_from_wish_list( $position, $list_id );
				$return['removed'] = true;
				$return['removed_from'] = $list_id;
				$return['position'] = $position;
			}
			// item not in list, add it
			else {
				edd_wl_add_to_wish_list( $_POST['download_id'], $options, $list_id );	
				
				$return['added'] = true;
				$return['added_to'] = $list_id;
			}

		}
		// ID of favorites list
		$return['fav_list'] = $list_id;

		echo json_encode( $return );
	}
	edd_die();
}
add_action( 'wp_ajax_edd_favorites_favorite', 'edd_favorites_favorite' );
add_action( 'wp_ajax_nopriv_edd_favorites_favorite', 'edd_favorites_favorite' );