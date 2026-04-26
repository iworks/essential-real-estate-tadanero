<?php
/**
 * Main plugin class file.
 *
 * @package WordPress_Plugin_Stub
 * @author Marcin Pietrzak <marcin@iworks.pl>
 * @copyright 2025-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license GPL-3.0-or-later
 * @link https://iworks.pl/
 *
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_essential_real_estate_tadanero' ) ) {
	return;
}

require_once __DIR__ . '/class-essential-real-estate-tadanero-base.php';

/**
 * Main plugin class.
 *
 * This class initializes the plugin and loads all necessary components.
 *
 * @since 1.0.0
 */
class iworks_essential_real_estate_tadanero extends iworks_essential_real_estate_tadanero_base {

	/**
	 * Plugin objects container.
	 *
	 * @since 1.0.0
	 * @var array $objects Array to store plugin objects.
	 */
	private array $objects = array();

	/**
	 * Class constructor.
	 *
	 * Initializes the plugin by setting up hooks and loading required files.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->version = 'PLUGIN_VERSION';
		/**
		 * WordPress Hooks
		 */
		add_action( 'init', array( $this, 'action_init_settings' ) );
		add_action( 'plugins_loaded', array( $this, 'action_plugins_loaded' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_wp_enqueue_scripts' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_wp_enqueue_scripts' ), PHP_INT_MAX );
		/**
		 * load github class
		 */
		$filename = $this->includes_directory . '/class-iworks-essential-real-estate-tadanero-github.php';
		if ( is_file( $filename ) ) {
			include_once $filename;
			new iworks_essential_real_estate_tadanero_github();
		}
		/**
		 * admin
		 */
		if ( is_admin() ) {
			$filename = $this->includes_directory . '/class-iworks-essential-real-estate-tadanero-wp-admin.php';
			if ( is_file( $filename ) ) {
				include_once $filename;
				new iworks_essential_real_estate_tadanero_wp_admin();
			}
		}
		/**
		 * is active?
		 */
		add_filter( 'essential-real-estate-tadanero/is_active', '__return_true' );
	}

	/**
	 * Initialize plugin settings and assets.
	 *
	 * Handles the initialization of plugin settings and enqueues frontend assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function action_init_settings() {
		/**
		 * options
		 */
		if ( is_admin() ) {
		} else {
			$file = 'assets/styles/essential-real-estate-tadanero-frontend' . $this->dev . '.css';
			wp_enqueue_style( 'essential-real-estate-tadanero', plugins_url( $file, $this->base ), array(), $this->get_version( $file ) );
		}
	}

	/**
	 * Plugin activation hook.
	 *
	 * Handles database installation and option initialization
	 * when the plugin is activated.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_activation_hook() {
		$this->db_install();
		$this->check_option_object();
		$this->options->activate();
		do_action( 'iworks/essential-real-estate-tadanero/register_activation_hook' );
	}

	/**
	 * Plugin deactivation hook.
	 *
	 * Handles cleanup tasks when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_deactivation_hook() {
		$this->check_option_object();
		$this->options->deactivate();
		do_action( 'iworks/essential-real-estate-tadanero/register_deactivation_hook' );
	}

	/**
	 * Database installation method.
	 *
	 * Handles the creation of required database tables.
	 * Currently empty as it's a stub implementation.
	 *
	 * @since 1.0.0
	 * @return void
	 * @todo Implement database table creation if needed.
	 */
	private function db_install() {
	}
	
	/**
	 * Plugins loaded hook.
	 *
	 * Handles tasks after all plugins are loaded.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function action_plugins_loaded() {
		do_action( 'iworks/essential-real-estate-tadanero/action_plugins_loaded' );
		$this->check_option_object();
		if ( $this->options->get_option( 'disable_ere_css' ) ) {
		}
	}

	public function action_wp_enqueue_scripts() {
		if ( $this->options->get_option( 'disable_ere_css' ) ) {
			foreach (
				array(
					'bootstrap',
					ERE_PLUGIN_PREFIX . 'main',
				)
				as $handle
			) {
				wp_deregister_style( $handle );
				wp_dequeue_style( $handle );
			}
		}
	}
}
