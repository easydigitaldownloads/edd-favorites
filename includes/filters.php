<?php
/**
 * Filters
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Disable sharing for favorites
 * @todo  this will be added back in soon
 */
add_filter( 'edd_wl_display_sharing', '__return_false' );

/**
 * Remove add all to cart link
 */
add_filter( 'edd_wl_show_add_all_to_cart_link', '__return_false' );

/**
 * Prevent variable priced options from showing
 */
add_filter( 'edd_wl_item_title_options', '__return_false' );


/**
 * Remove CSS class responsible for ajax when there are variable priced downloads
 *
 * @since 1.0.4
 */
function edd_favorites_edd_wl_item_purchase( $classes, $download_id ) {
	$variable_pricing = edd_has_variable_prices( $download_id );

	if ( $variable_pricing )
		return '';

	return $classes;
}
add_filter( 'edd_wl_item_purchase_default_css_classes', 'edd_favorites_edd_wl_item_purchase', 10, 2 );

/**
 * Filter the price on the favorites page
 * Variable prices will show a price range
 *
 * @since 1.0
 */
function edd_favorites_edd_wl_item_price( $item_price, $item_id ) {
	$variable_pricing 	= edd_has_variable_prices( $item_id );

	if ( $variable_pricing ) {
		$item_price = edd_price_range( $item_id );
	}

	return $item_price;
}
add_filter( 'edd_wl_item_price', 'edd_favorites_edd_wl_item_price', 10, 2 );

/**
 * Delete Favorites link on edit page
 * @since  1.0.3
 */
function edd_favorites_delete_list_text( $args ) {
	if ( edd_favorites_is_edit_page() ) {
		$args['text'] = apply_filters( 'edd_favorites_delete_list_text', sprintf( __( 'Delete %s', 'edd-favorites' ), edd_favorites_get_label_plural( true ) ) );
	}

	return $args;
}
add_filter( 'edd_wl_delete_list_link_defaults', 'edd_favorites_delete_list_text' );

/**
 * Change the labels
*/
function edd_favorites_default_labels() {
	$defaults = array(
	   'singular' 	=> __( 'Favorite', 'edd-favorites' ),
	   'plural' 	=> __( 'Favorites', 'edd-favorites')
	);

	return apply_filters( 'edd_favorites_default_labels', $defaults );
}

/**
 * Messages
 * 
 * @since 1.0
 */
function edd_favorites_messages( $messages ) {

	$new_messages = array();

	// main favorite page
	if ( edd_favorites_is_favorites() ) {
		$new_messages = array(
			'list_updated'			=> __( 'Successfully updated', 'edd-favorites' ),
			'modal_share_title'		=> sprintf( __( 'Share your %s', 'edd-favorites' ), edd_favorites_get_label_plural( true )),
		);
	}

	// edit page
	if ( edd_favorites_is_edit_page() ) {
		$new_messages = array(
			'list_delete_confirm' 			=> sprintf( __( 'You are about to delete your %s, are you sure?', 'edd-wish-lists' ), edd_favorites_get_label_plural( true ) ),
			'modal_delete_title'			=> sprintf( __( 'Delete %s', 'edd-wish-lists' ), edd_favorites_get_label_plural( true ) ),
			'modal_button_delete_confirm'	=> sprintf( __( 'Yes, delete %s', 'edd-wish-lists' ), edd_favorites_get_label_plural( true ) ),	
		);
	}

	$new_messages = apply_filters( 'edd_favorites_messages', $new_messages );

	return array_merge( $messages, $new_messages );
}
add_filter( 'edd_wl_messages', 'edd_favorites_messages' );


/**
 * Filter the purchase button
 * Variable prices will show a 'view' button instead because they cannot be purchased
 *
 * @since 1.0
 */
function edd_favorites_edd_wl_add_to_cart_defaults( $defaults, $item_id ) {
	$variable_pricing 	= edd_has_variable_prices( $item_id );

	if ( ! $variable_pricing )
		return $defaults;

	$new_defaults = array(
		'text' 			=> 'View',
		'class'			=> 'edd-wl-action',
		'style'			=> 'button',
		'link'			=> get_permalink( $item_id )
	);

	return array_merge( $defaults, $new_defaults );
}
add_filter( 'edd_wl_add_to_cart_defaults', 'edd_favorites_edd_wl_add_to_cart_defaults', 10, 2 );


/**
 * Settings
 *
 * @since 1.0
*/
function edd_favorites_settings( $settings ) {
	$plugin_settings = array(
		array(
			'id' => 'edd_favorites_page_view',
			'name' => sprintf( __( '%s Page', 'edd-favorites' ), edd_favorites_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will view their %s. This page should include the [edd_favorites] shortcode', 'edd-favorites' ), edd_favorites_get_label_plural( true ) ) . '</p>',
			'type' => 'dropdown_pages',
		),
		array(
			'id' => 'edd_favorites_page_edit',
			'name' => sprintf( __( '%s Edit Page', 'edd-favorites' ), edd_favorites_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will edit their %s. This page should include the [edd_favorites_edit] shortcode', 'edd-favorites' ), edd_favorites_get_label_plural( true ) ) . '</p>',
			'type' => 'dropdown_pages',
		),
		array(
			'id' => 'edd_favorites_favorite',
			'name' => sprintf( __( '%s Text', 'edd-favorites' ), edd_favorites_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Enter the text you\'d like to appear for favoriting a %s', 'edd-favorites' ), edd_get_label_singular( true ) ) . '</p>',
			'type' => 'text',
			'std'  => __( 'Favorite', 'edd-favorites' )
		),
	);

	return array_merge( $settings, $plugin_settings );
}
add_filter( 'edd_settings_extensions', 'edd_favorites_settings', 100 );

/**
 * Add favorites to the list of allowed pages that load scripts etc
 */
function edd_favorites_edd_wl_is_view_page( $pages ) {
	// add our page ID
	$pages[] = edd_favorites_page_id();

	return $pages;
}
add_filter( 'edd_wl_is_view_page', 'edd_favorites_edd_wl_is_view_page' );

/**
 * Modify the edit settings link to go to our new edit page for favorites
 */
function edd_favorites_edd_wl_edit_settings_link_uri( $uri, $list_id ) {
	// list ID matches the favorites list ID and we're on favorites page

	if ( $list_id == edd_favorites_get_users_list_id() && edd_favorites_is_favorites() ) {
		$uri = edd_favorites_get_edit_uri( $list_id );
	}

	return $uri;
}
add_filter( 'edd_wl_edit_settings_link_uri', 'edd_favorites_edd_wl_edit_settings_link_uri', 10, 2 );

/**
 * Make sure that when the favorites list is updated, return to the favorites page
 * If the favorites list is edited from the wish lists page, then it still returns to the wish list view page
 */
function edd_favorites_post_type_link( $post_link, $post, $leavename, $sample ) {
    if ( $post->post_type == 'edd_wish_list' ) {
        if ( edd_favorites_is_edit_page() && get_query_var( 'edit' ) == edd_favorites_get_users_list_id() ) {
        	$post_link = edd_favorites_get_view_uri( $post->ID );
        }
    }

    return $post_link;
}
add_filter( 'post_type_link', 'edd_favorites_post_type_link', 11, 4 );

/**
 * Filter the shortlink for favorites
 * @todo coming in future version
 */
function edd_favorites_get_shortlink( $shortlink, $id, $context, $allow_slugs ) {

	if ( edd_favorites_is_favorites() ) {
    	$shortlink = home_url( 'fav/' . $id );
	}

	return $shortlink;
}
//add_filter( 'get_shortlink', 'edd_favorites_get_shortlink', 10, 4 );

/**
 * Hide 'edit settings' link on favorites page if page not selected in options
 * @param  [type] $rule [description]
 * @return [type]       [description]
 */
function edd_favorites_edd_wl_edit_settings_link_return( $rule ) {
	if ( edd_favorites_is_favorites() ) {
		$rule = 'none' == edd_get_option( 'edd_favorites_page_edit' );
	}

	return $rule;
}
add_filter( 'edd_wl_edit_settings_link_return', 'edd_favorites_edd_wl_edit_settings_link_return', 10, 1 ); 
