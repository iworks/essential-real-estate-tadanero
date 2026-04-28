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

		/**
		 * Add filter for custom property fields
		 */
		add_filter( 'eret_get_property_fields', array( $this, 'eret_get_property_fields' ) );
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


	/**
	 * Filter and add custom property fields to overview
	 *
	 * Retrieves custom meta fields for the current property and adds them to the
	 * property overview array with proper formatting and priority.
	 *
	 * @since 1.0.0
	 * @param array $fields Existing property fields array
	 * @return array Modified property fields array with custom fields
	 */
	public function eret_get_property_fields( $fields ) {
		$property_id = get_the_ID();
		$priority     = 140;
		$field_config = $this->get_fields();
		$additional_fields = array();

		// Flatten the field configuration to get all sub-fields
		foreach ( $field_config as $field_group ) {
			if ( isset( $field_group['fields'] ) && is_array( $field_group['fields'] ) ) {
				foreach ( $field_group['fields'] as $sub_field ) {
					$additional_fields[] = $sub_field;
				}
			}
		}

		// Process each additional field
		foreach ( $additional_fields as $field ) {
			$property_field        = get_post_meta( $property_id, $field['id'], true );
			$property_field_content = $property_field;

			// Handle checkbox_list field type
			if ( 'checkbox_list' === $field['type'] ) {
				$property_field_content = '';
				if ( is_array( $property_field ) ) {
					foreach ( $property_field as $value => $v ) {
						$property_field_content .= $v . ', ';
					}
					$property_field_content = rtrim( $property_field_content, ', ' );
				}
			}

			// Handle textarea field type
			if ( 'textarea' === $field['type'] ) {
				$property_field_content = wpautop( $property_field_content );
			}

			// Add field to overview if it has content
			if ( ! empty( $property_field_content ) ) {
				$fields[ $field['id'] ] = array(
					'title'    => $field['title'],
					'content'  => '<span>' . $property_field_content . '</span>',
					'priority' => $priority,
				);
			}

			$priority += 10;
		}

		return $fields;
	}
}
