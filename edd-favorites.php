<?php
/*
Plugin Name: Easy Digital Downloads - Favorites
Plugin URI: http://sumobi.com/shop/edd-favorites
Description: An add-on for EDD Wish Lists. Favorite/Unfavorite downloads in just 1 click.
Version: 1.0.5
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Favorites' ) ) :

	final class EDD_Favorites {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of EDD Favorites exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Favorites ) ) {
				self::$instance = new EDD_Favorites;
				self::$instance->setup_globals();
				self::$instance->hooks();
				self::$instance->includes();
				
			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 * @see EDD_Favorites::init()
		 * @see EDD_Favorites::activation()
		 */
		private function __construct() {
			self::$instance = $this;

			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Globals
		 *
		 * @since 1.0
		 * @return void
		 */
		private function setup_globals() {
			$this->version 		= '1.0.5';
			$this->title 		= 'EDD Favorites';

			// paths
			$this->file         = __FILE__;
			$this->basename     = apply_filters( 'edd_favorites_plugin_basenname', plugin_basename( $this->file ) );
			$this->plugin_dir   = apply_filters( 'edd_favorites_plugin_dir_path',  plugin_dir_path( $this->file ) );
			$this->plugin_url   = apply_filters( 'edd_favorites_plugin_dir_url',   plugin_dir_url ( $this->file ) );
		}

		/**
		 * Function fired on init
		 *
		 * This function is called on WordPress 'init'. It's triggered from the
		 * constructor function.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @uses EDD_Favorites::load_textdomain()
		 *
		 * @return void
		 */
		public function init() {
			do_action( 'edd_favorites_before_init' );

			$this->load_textdomain();

			do_action( 'edd_favorites_after_init' );
		}

		/**
		 * Includes
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private function includes() {
			require_once( dirname( $this->file ) . '/includes/emails.php' );
			require_once( dirname( $this->file ) . '/includes/filters.php' );
			require_once( dirname( $this->file ) . '/includes/functions.php' );
			require_once( dirname( $this->file ) . '/includes/template-functions.php' );
			require_once( dirname( $this->file ) . '/includes/ajax-functions.php' );
			require_once( dirname( $this->file ) . '/includes/shortcodes.php' );
			require_once( dirname( $this->file ) . '/includes/scripts.php' );

		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			add_action( 'admin_init', array( $this, 'activation' ) );
			
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			// insert actions
			do_action( 'edd_favorites_setup_actions' );
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_favorites_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-favorites' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-favorites', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-favorites/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'edd-favorites', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'edd-favorites', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-favorites', false, $lang_dir );
			}
		}

		/**
		 * Activation function fires when the plugin is activated.
		 *
		 * This function is fired when the activation hook is called by WordPress,
		 * it flushes the rewrite rules and disables the plugin if EDD isn't active
		 * and throws an error.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @return void
		 */
		public function activation() {
			global $wpdb;

			if ( ! class_exists( 'Easy_Digital_Downloads' ) || ! class_exists( 'EDD_Wish_Lists' ) ) {
				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					// deactivate the plugin
			 		deactivate_plugins( plugin_basename( __FILE__ ) );
			 		// unset activation notice
			 		unset( $_GET[ 'activate' ] );
			 		// display notice
			 		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
			}
			else {
				add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'settings_link' ), 10, 2 );
			}

		}

		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {
			$edd_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/easy-digital-downloads/easy-digital-downloads.php', false, false );

			if ( ! is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
				echo '<div class="error"><p>' . sprintf( __( 'You must install %sEasy Digital Downloads%s to use EDD Favorites', 'edd-favorites' ), '<a href="http://wordpress.org/plugins/easy-digital-downloads/" title="Easy Digital Downloads" target="_blank">', '</a>' ) . '</p></div>';
			}

			if ( ! is_plugin_active( 'edd-wish-lists/edd-wish-lists.php' ) ) {
				echo '<div class="error"><p>' . sprintf( __( 'You must install %sEDD Wish Lists%s to use EDD Favorites', 'edd-favorites' ), '<a href="https://easydigitaldownloads.com/extensions/edd-wish-lists/?ref=166" title="EDD Wish Lists" target="_blank">', '</a>' ) . '</p></div>';
			}

			if ( $edd_plugin_data['Version'] < '1.8.4' ) {
				echo '<div class="error"><p>' . __( 'EDD Favorites requires Easy Digital Downloads Version 1.9 or greater. Please update Easy Digital Downloads.', 'edd-favorites' ) . '</p></div>';
			}
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a href="https://easydigitaldownloads.com/blog/author/andrewmunro/?ref=166" target="_blank">' . __( 'Author\'s EDD plugins', 'edd-favorites' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}

		/**
		 * Plugin settings link
		 *
		 * @since 1.0
		*/
		public function settings_link( $links ) {
			$plugin_links = array(
				'<a title="View more plugins for Easy Digital Downloads by Sumobi" href="' . admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) . '">' . __( 'Settings', 'edd-favorites' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

	}


/**
 * Loads a single instance of EDD Favorites
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $edd_favorites = edd_favorites_load(); ?>
 *
 * @since 1.0
 *
 * @see EDD_Favorites::get_instance()
 *
 * @return object Returns an instance of the EDD_Favorites class
 */
function edd_favorites_load() {
	return EDD_Favorites::get_instance();
}

/**
 * Loads plugin after all the others have loaded and have registered their hooks and filters
 *
 * @since 1.0
*/
add_action( 'plugins_loaded', 'edd_favorites_load', apply_filters( 'edd_favorites_action_priority', 100 ) );

endif;