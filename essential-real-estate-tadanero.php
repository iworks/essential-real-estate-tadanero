<?php
/*
Plugin Name: Essential Real Estate - Tadanero
Text Domain: essential-real-estate-tadanero
Plugin URI: PLUGIN_URI
Requires Plugins: essential-real-estate
Description: PLUGIN_TAGLINE
Version: PLUGIN_VERSION
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Copyright 2025-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 3, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Define static options and constants for the plugin
 */
// Define plugin version constant
define( 'IWORKS_ESSENTIAL_REAL_ESTATE_TADANERO_VERSION', 'PLUGIN_VERSION' );
// Define prefix for all plugin options and functions
define( 'IWORKS_ESSENTIAL_REAL_ESTATE_TADANERO_PREFIX', 'iworks_essential-real-estate-tadanero_' );
// Get the base directory path
$base = __DIR__;
// Set vendor directory path (where core classes are located)
$vendor = $base . '/includes';

/**
 * Load the main plugin class if it doesn't exist
 * This is the core class that handles all plugin functionality
 */
if ( ! class_exists( 'iworks_essential_real_estate_tadanero' ) ) {
	// Load the main plugin class from the includes directory
	require_once $vendor . '/iworks/class-essential-real-estate-tadanero.php';
}

/**
 * Load configuration options
 * This file contains all plugin configuration settings
 */
require_once $base . '/etc/options.php';

/**
 * Load the options class if it doesn't exist
 * This class handles all plugin options and settings
 */
if ( ! class_exists( 'iworks_options' ) ) {
	// Load the options class from the includes directory
	require_once $vendor . '/iworks/options/options.php';
}

/**
 * Post Type Filters
 * These filters control which custom post types are loaded by the plugin
 * Each filter returns false by default - change to true to enable the post type
 */
// FAQ post type
add_filter( 'essential-real-estate-tadanero/load/posttype/faq', '__return_false' );
// Hero post type
add_filter( 'essential-real-estate-tadanero/load/posttype/hero', '__return_false' );
// Opinion post type
add_filter( 'essential-real-estate-tadanero/load/posttype/opinion', '__return_false' );
// Custom page post type
add_filter( 'essential-real-estate-tadanero/load/posttype/page', '__return_false' );
// Person post type
add_filter( 'essential-real-estate-tadanero/load/posttype/person', '__return_false' );
// Custom post post type
add_filter( 'essential-real-estate-tadanero/load/posttype/post', '__return_false' );
// Project post type
add_filter( 'essential-real-estate-tadanero/load/posttype/project', '__return_false' );
// Promo post type
add_filter( 'essential-real-estate-tadanero/load/posttype/promo', '__return_false' );
// Publication post type
add_filter( 'essential-real-estate-tadanero/load/posttype/publication', '__return_false' );

/**
 * Initialize and get plugin options
 * This function creates and returns the options object
 *
 * @return iworks_options The plugin options object
 */
function iworks_essential_real_estate_tadanero_get_options() {
	// Use global variable to store options object
	global $iworks_essential_real_estate_tadanero_options;

	// Return existing options object if it exists
	if ( is_object( $iworks_essential_real_estate_tadanero_options ) ) {
		return $iworks_essential_real_estate_tadanero_options;
	}

	// Create new options object if it doesn't exist
	$iworks_essential_real_estate_tadanero_options = new iworks_options();

	// Set the function name for options
	$iworks_essential_real_estate_tadanero_options->set_option_function_name( 'iworks_essential_real_estate_tadanero_options' );
	// Set the option prefix for all plugin options
	$iworks_essential_real_estate_tadanero_options->set_option_prefix( IWORKS_ESSENTIAL_REAL_ESTATE_TADANERO_PREFIX );

	// Set the plugin file name if the method exists
	if ( method_exists( $iworks_essential_real_estate_tadanero_options, 'set_plugin' ) ) {
		$iworks_essential_real_estate_tadanero_options->set_plugin( basename( __FILE__ ) );
	}

	// Initialize the options
	$iworks_essential_real_estate_tadanero_options->options_init();

	// Return the options object
	return $iworks_essential_real_estate_tadanero_options;
}

// Initialize the main plugin class
$iworks_essential_real_estate_tadanero = new iworks_essential_real_estate_tadanero();

/**
 * Register plugin activation and deactivation hooks
 */
// Register activation hook to run when plugin is activated
register_activation_hook( __FILE__, array( $iworks_essential_real_estate_tadanero, 'register_activation_hook' ) );
// Register deactivation hook to run when plugin is deactivated
register_deactivation_hook( __FILE__, array( $iworks_essential_real_estate_tadanero, 'register_deactivation_hook' ) );
