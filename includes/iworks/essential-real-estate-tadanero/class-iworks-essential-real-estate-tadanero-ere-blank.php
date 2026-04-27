<?php
/**
 * Tadanero: Essential Real Estate - Blank
 *
 * This file contains the class for blank functionality.
 * It provides a base template for creating new Essential Real Estate modifications.
 *
 * @package    iWorks
 * @subpackage Tadanero: Essential Real Estate
 * @author     Marcin Pietrzak <marcin@iworks.pl>
 * @copyright  2025-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 * @version    1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_essential_real_estate_tadanero_ere_blank' ) ) {
	return;
}

require_once dirname( __DIR__ ) . '/class-essential-real-estate-tadanero-base.php';

/**
 * Class for blank functionality
 *
 * This class serves as a base template for creating new Essential Real Estate modifications.
 * It can be used as a starting point for new features and customizations.
 *
 * @since 1.0.0
 */
class iworks_essential_real_estate_tadanero_ere_blank extends iworks_essential_real_estate_tadanero_base {

	/**
	 * Constructor
	 *
	 * Initialize the blank functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
	}

}
