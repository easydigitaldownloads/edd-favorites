<?php
/**
 * Emails
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Filter email subject line for favorites
 * @param  [type] $subject [description]
 * @return [type]          [description]
 */
function edd_favorites_share_via_email_subject( $subject, $sender_name, $referrer ) {
	// if share form was sent from favorites page
	if ( $referrer == edd_favorites_page_id() ) {
		$subject = sprintf( __( '%s has suggested you look at their %s from %s', 'edd-favorites' ), $sender_name, edd_favorites_get_label_plural( true ), get_bloginfo('name') );
	}

	return $subject;
}
add_filter( 'edd_wl_share_via_email_subject', 'edd_favorites_share_via_email_subject', 10, 3 );


/**
 * Filter email body
 * @param  [type] $subject [description]
 * @return [type]          [description]
 */
function edd_favorites_share_via_email_message( $default_email_body, $shortlink, $sender_name, $message, $sender_email, $referrer ) {
	// if share form was sent from favorites page
	if ( $referrer == edd_favorites_page_id() ) {
		// Email body
		$default_email_body = __( "Hi!", "edd-wish-lists" ) . "<br/><br/>";
		$default_email_body .= sprintf( __( "%s has suggested you look at their %s from %s:", "edd-wish-lists" ), $sender_name, edd_favorites_get_label_plural( true ), get_bloginfo( 'name' ) ) . "<br/>";
		$default_email_body .= $shortlink . "<br/><br/>";

		if ( $message )
			$default_email_body .= $message . "<br/><br/>";
		
		$default_email_body .= sprintf( __( "Reply to %s by emailing %s", "edd-wish-lists" ), $sender_name, '<a href="mailto:' . $sender_email . '" title="' . $sender_email . '">' . $sender_email . '</a>' ) . "<br/><br/>";
		$default_email_body .= get_bloginfo('name') . "<br/>";
		$default_email_body .= '<a title="' . get_bloginfo( 'name' ) . '" href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'url' ) . '</a>';

	}

	return apply_filters( 'edd_favorites_share_via_email_message', $default_email_body, $shortlink, $sender_name, $message, $sender_email, $referrer );;
}
add_filter( 'edd_wl_share_via_email_message', 'edd_favorites_share_via_email_message', 10, 6 );