<?php
/**
 * Shortcodes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Favorites edit shortcode
 *
 * @since 1.0
*/
function edd_favorites_edit_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'id' => '',
			'title' => '',
		), $atts, 'edd_favorites_edit' )
	);

	if ( edd_wl_is_private_list() )
		return;

	$content = edd_wl_load_template( 'edit' );

	return $content;
}
add_shortcode( 'edd_favorites_edit', 'edd_favorites_edit_shortcode' );

/**
 * Favorites shortcode
 * @since  1.0
 */
function edd_favorites_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'id' => '',
			'title' => '',
		), $atts, 'edd_favorites' )
	);

	// prevent list from displaying if it's private
	if ( edd_wl_is_private_list() )
		return;

	$content = edd_wl_load_template( 'view' );

	return $content;
}
add_shortcode( 'edd_favorites', 'edd_favorites_shortcode' );

/**
 * Modify 
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function edd_favorites_modify_shortcode( $args, $download_id, $option ) {
	$classes[] = 'edd-wl-action';
	$classes[] = 'edd-wl-favorite';

	$prices = edd_get_variable_prices( $download_id );

	$options = array();
	$options['price_id'] = $option - 1;

	$classes[] = edd_wl_item_in_wish_list( $download_id, $options, edd_favorites_get_users_list_id() ) ? edd_favorites_css_class() : '';

	$defaults = array(
		'action'		=> 'edd_favorites_favorite',
		'class'			=> implode( ' ', $classes ),
	);

	return array_merge( $args, $defaults );
}
add_filter( 'edd_wl_add_to_list_shortcode', 'edd_favorites_modify_shortcode', 10, 3 );