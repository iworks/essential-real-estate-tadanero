<?php
/**
 * Tadanero: Essential Real Estate - Common
 *
 * This file contains the class for common Essential Real Estate functionality.
 * It provides shared features and customizations for property listings and meta fields.
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
 * Class for common Essential Real Estate functionality
 *
 * This class handles shared functionality for Essential Real Estate plugin,
 * including custom meta fields, template modifications, and property display enhancements.
 *
 * @since 1.0.0
 */
class iworks_essential_real_estate_tadanero_ere_common extends iworks_essential_real_estate_tadanero_base {

	/**
	 * Constructor
	 *
	 * Initialize the common functionality for Essential Real Estate modifications.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialize hooks and filters
	 *
	 * Sets up WordPress actions and filters for property display modifications.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		// Remove default actions
		remove_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', 'ere_template_loop_property_excerpt', 20 );
		remove_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', 'ere_template_loop_property_link_detail', 25 );

		// Add custom actions
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', array( $this, 'meta_section_start' ), 100 );
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', 'ere_template_loop_property_size', 130 );
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', array( $this, 'loop_property_rooms' ), 135 );
		add_action( 'ere_sc_property_featured_layout_property_list_two_columns_loop_property_content', array( $this, 'meta_section_end' ), 200 );

		// Add meta box filter
		add_filter( 'ere_register_meta_boxes_property_main', array( $this, 'ere_register_meta_boxes_property_main_add_fields' ) );
	}

	/**
	 * Output opening meta section wrapper
	 *
	 * Renders the opening div tag for the property meta section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function meta_section_start() {
		echo '<div class="ere-meta-section">';
	}

	/**
	 * Output closing meta section wrapper
	 *
	 * Renders the closing div tag for the property meta section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function meta_section_end() {
		echo '</div>';
	}

	/**
	 * Add custom fields to property meta boxes
	 *
	 * Adds additional custom fields to the Essential Real Estate property meta boxes
	 * in the real estate details tab.
	 *
	 * @since 1.0.0
	 * @param array $config Existing meta box configuration
	 * @return array Modified meta box configuration
	 */
	public function ere_register_meta_boxes_property_main_add_fields( $config ) {
		foreach ( $config as &$tab ) {
			if ( 'real_estate_details_tab' === $tab['id'] ) {
				$tab['fields'] = array_merge( $tab['fields'], $this->get_fields() );
			}
		}
		return $config;
	}

	/**
	 * Display property rooms in loop
	 *
	 * Renders the property rooms information for property listings.
	 *
	 * @since 1.0.0
	 * @param int $property_id Optional. Property ID. Defaults to current post ID.
	 * @return void
	 */
	public function loop_property_rooms( $property_id = '' ) {
		if ( empty( $property_id ) ) {
			$property_id = get_the_ID();
		}
		$property_rooms = get_post_meta( $property_id, ERE_METABOX_PREFIX . 'property_rooms', true );
		if ( '' === $property_rooms ) {
			return;
		}
		ere_get_template(
			'loop/property-rooms.php',
			array(
				'property_rooms' => $property_rooms,
			)
		);
	}
	/**
	 * Get custom meta fields configuration
	 *
	 * Returns an array of custom meta fields to be added to property meta boxes.
	 * Includes various property details like rent, condition, building info, etc.
	 *
	 * @since 1.0.0
	 * @return array Array of custom meta field configurations
	 */
	private function get_fields() {
		$fields = array(
			array(
				'type' => 'divide',
			),
			array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'rent_monthly',
						'title'   => __( 'Monthly Rent', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: 4200', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'other_fees',
						'title'   => __( 'Other Fees', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: 500', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
				array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'condition',
						'title'   => __( 'Condition', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: New/Used', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'finishing_standard',
						'title'   => __( 'Finishing Standard', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: high/standard/low', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
			array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'building_type',
						'title'   => __( 'Building Type', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: apartment/house', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'building_material',
						'title'   => __( 'Building Material', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: concrete/brick', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
			array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'form_of_ownership',
						'title'   => __( 'Form of Ownership', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: freehold/leasehold', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'market_status',
						'title'   => __( 'Market Status', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: primary/secondary', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
			array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'loudness',
						'title'   => __( 'Loudness', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: quiet/noisy', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'staircase',
						'title'   => __( 'Staircase', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: clean/old', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
			array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'windows',
						'title'   => __( 'Windows', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: PVC/wood', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'walls',
						'title'   => __( 'Walls', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: plaster/brick/stone', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
			array(
				'type' => 'row',
				'col' => 6,
				'fields' => array(
					array(
						'id'      => ERE_METABOX_PREFIX . 'balcony_type',
						'title'   => __( 'Balcony Type', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: none/wooden/carpet/tiles', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'available_from',
						'title'   => __( 'Available From', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: 2025-01-01', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
				),
			),
		);
		return $fields;
	}
}
