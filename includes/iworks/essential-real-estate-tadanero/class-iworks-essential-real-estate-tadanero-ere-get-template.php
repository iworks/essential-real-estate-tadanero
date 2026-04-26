<?php
/**
 * Tadanero: Essential Real Estate - Template Part Override
 *
 * This file contains the class for overriding Essential Real Estate template parts.
 * It provides functionality to customize and modify template rendering.
 *
 * @package    iWorks
 * @subpackage Tadanero: Essential Real Estate
 * @author     Marcin Pietrzak <marcin@iworks.pl>
 * @copyright  2025-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 * @version    1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_essential_real_estate_tadanero_ere_get_template' ) ) {
	return;
}

require_once dirname( __DIR__ ) . '/class-essential-real-estate-tadanero-base.php';

/**
 * Class for overriding Essential Real Estate template parts
 *
 * This class handles the customization of template parts used by the Essential Real Estate plugin,
 * allowing for frontend modifications and custom rendering.
 *
 * @since 1.0.0
 */
class iworks_essential_real_estate_tadanero_ere_get_template extends iworks_essential_real_estate_tadanero_base {

	/**
	 * Constructor
	 *
	 * Initialize the template part override functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		// Hook into Essential Real Estate template loading
		add_filter( 'ere_get_template', array( $this, 'override_template' ), 10, 4 );
		add_filter( 'ere_locate_template', array( $this, 'override_locate_template' ), 10, 3 );
	}

	/**
	 * Override locate template
	 *
	 * Filter the template path to use custom templates when available.
	 *
	 * @param string $template Template path
	 * @param string $template_name Template name
	 * @param string $template_path Template path
	 * @return string Modified template path
	 * @since 1.0.0
	 */
	public function override_locate_template( $template, $template_name, $template_path ) {
		// Check if we have a custom template in our plugin
		$custom_template = $this->get_custom_template_path( $template_name );
		if ( $custom_template && file_exists( $custom_template ) ) {
			return $custom_template;
		}
		
		return $template;
	}

	/**
	 * Override template parts
	 *
	 * Filter the template part path to use custom templates when available.
	 *
	 * @param string $template Template path
	 * @param string $slug Template slug
	 * @param string $name Template name
	 * @param array  $args Template arguments
	 * @return string Modified template path
	 * @since 1.0.0
	 */
	public function override_template( $template, $slug, $name, $args ) {
		// Check if we have a custom template in our plugin
		$custom_template = $this->get_custom_template_path( $slug );
		if ( $custom_template && file_exists( $custom_template ) ) {
			return $custom_template;
		}
		
		return $template;
	}

}
