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

if ( class_exists( 'iworks_essential_real_estate_tadanero_ere_common' ) ) {
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
class iworks_essential_real_estate_tadanero_ere_common extends iworks_essential_real_estate_tadanero_base {

	/**
	 * Constructor
	 *
	 * Initialize the template part override functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'ere_register_meta_boxes_property_main', array( $this, 'ere_register_meta_boxes_property_main_add_fields' ) );
	}

	public function init() {
		remove_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', 'ere_template_loop_property_excerpt', 20 );
		remove_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', 'ere_template_loop_property_link_detail', 25 );
		/**
		 * Add property size after link detail
		 */
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', array( $this, 'meta_section_start' ), 100 );
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', 'ere_template_loop_property_size', 130 );
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', array( $this, 'loop_property_rooms' ), 135 );
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', array( $this, 'meta_section_end' ), 200 );



	}

	public function meta_section_start() {
		echo '<div class="ere-meta-section">';
	}

	public function meta_section_end() {
		echo '</div>';
	}

	public function ere_register_meta_boxes_property_main_add_fields( $config ) {
		foreach ( $config as &$tab ) {
			if ( 'real_estate_details_tab' === $tab['id'] ) {
				$tab['fields'][] = array(
					array( 'type' => 'divide' ),
				);
				$tab['fields'][] = array(
					'name' => __( 'Additional Information', 'essential-real-estate-tadanero' ),
					'id'   => 'additional_information',
					'type' => 'textarea',
				);
			}
		}
		return $config;
	}

public	function loop_property_rooms($property_id = '') {
    if (empty($property_id)) {
        $property_id = get_the_ID();
    }
    $property_rooms = get_post_meta( $property_id, ERE_METABOX_PREFIX . 'property_rooms', true );
    if ( $property_rooms === '' ) {
        return;
    }
    ere_get_template( 'loop/property-rooms.php', array(
        'property_rooms' => $property_rooms,
    ) );
}
}
