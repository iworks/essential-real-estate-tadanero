<?php
/**
 * Tadanero: Essential Real Estate - Single Property
 *
 * This file contains the class for handling single property functionality.
 * It provides customization for single property pages and displays.
 *
 * @package    iWorks
 * @subpackage Tadanero: Essential Real Estate
 * @author     Marcin Pietrzak <marcin@iworks.pl>
 * @copyright  2025-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 * @version    1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_essential_real_estate_tadanero_ere_single_property' ) ) {
	return;
}

require_once dirname( __DIR__ ) . '/class-essential-real-estate-tadanero-base.php';

/**
 * Class for handling single property functionality
 *
 * This class handles the customization and enhancement of single property pages
 * used by the Essential Real Estate plugin, allowing for frontend modifications
 * and custom rendering of individual property listings.
 *
 * @since 1.0.0
 */
class iworks_essential_real_estate_tadanero_ere_single_property extends iworks_essential_real_estate_tadanero_base {

	/**
	 * Constructor
	 *
	 * Initialize the single property functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'ere_single_property_before_summary', array( $this, 'single_property_before_summary' ), 10 );
		add_action( 'ere_single_property_after_summary', array( $this, 'single_property_after_summary' ), 10 );
		add_action( 'ere_single_property_summary', array( $this, 'single_property_summary' ), 54 );
	}

	public function single_property_before_summary() {
		echo '<div class="tadanero-single-property">';
		echo '<div class="tadanero-single-property-content">';
	}

	public function single_property_after_summary() {
		echo '</aside>';
		echo '</div>';
	}

	public function single_property_summary() {
		echo '</div>';
		echo '<aside class="tadanero-single-property-sidebar">';
	}

}
