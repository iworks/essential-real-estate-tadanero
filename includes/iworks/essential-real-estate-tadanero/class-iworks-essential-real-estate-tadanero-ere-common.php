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
	public function ere_register_meta_boxes_property_main_add_fields( $cfg ) {
		$config = array();
		foreach ( $cfg as $tab ) {
			switch ( $tab['id'] ) {
			case 'real_estate_details_tab':
				$fields = array();
				foreach ( $tab['fields'] as &$tab_fields ) {
					if ( isset( $tab_fields['fields'] ) ) {
						foreach ( $tab_fields['fields'] as $index => $tab_fields_field ) {
							switch( $tab_fields_field['id'] ) {
								case ERE_METABOX_PREFIX . 'property_garage':
								case ERE_METABOX_PREFIX . 'property_garage_size':
									$tab_fields_field['group'] = 'car-related';
									$fields[] = $tab_fields_field;
									unset( $tab_fields['fields'][$index] );
									break;
								case ERE_METABOX_PREFIX . 'property_size':
								case ERE_METABOX_PREFIX . 'property_land':
									$tab_fields_field['group'] = 'building';
									$fields[] = $tab_fields_field;
									unset( $tab_fields['fields'][$index] );
									break;
							}
						}
					}
				}
				$tab['fields'] = wp_parse_args($this->add_other(), $tab['fields']);
				$config[] = $tab;
				/**
				 * tabs
				 */
				$config[] = $this->add_tab_building($fields);
				$config[] = $this->add_tab_costs($fields);
				$config[] = $this->add_tab_media($fields);
				$config[] = $this->add_tab_car_related($fields);
				break;
			case 'real_estate_floors_tab':
				// remove this
				break;
			default:
				$config[] = $tab;
				break;
			}
		}
		return $config;
	}

	/**
	 * Add building tab to meta box configuration
	 *
	 * Creates a new tab for building-related fields in the property meta boxes.
	 *
	 * @since 1.0.0
	 * @param array $fields Array of fields to include in the tab
	 * @return array Building tab configuration
	 */
	private function add_tab_building( $fields ) {
		return array(
			'id'     => 'tadanero_building_tab',
			'title'  => __( 'Building', 'essential-real-estate-tadanero' ),
			'icon'   => 'dashicons dashicons-bank',
			'fields' => $this->get_fields_by_group( 'building', $fields ),
		);
	}


	private function add_tab_costs( $fields ) {
		return array(
			'id'     => 'tadanero_costs_tab',
			'title'  => __( 'Costs', 'essential-real-estate-tadanero' ),
			'icon'   => 'dashicons dashicons-money',
			'fields' => $this->get_fields_by_group( 'costs', $fields ),
		);
	}

	/**
	 * Add media tab to meta box configuration
	 *
	 * Creates a new tab for media-related fields in the property meta boxes.
	 *
	 * @since 1.0.0
	 * @param array $fields Array of fields to include in the tab
	 * @return array Media tab configuration
	 */
	private function add_tab_media( $fields ) {
		return array(
			'id'     => 'tadanero_media_tab',
			'title'  => __( 'Media & Energy', 'essential-real-estate-tadanero' ),
			'icon'   => 'dashicons dashicons-admin-plugins',
			'fields' => $this->get_fields_by_group( 'media', $fields ),
		);
	}

	/**
	 * Add car related tab to meta box configuration
	 *
	 * Creates a new tab for car-related fields in the property meta boxes.
	 *
	 * @since 1.0.0
	 * @param array $fields Array of fields to include in the tab
	 * @return array Car related tab configuration
	 */
	private function add_tab_car_related( $fields ) {
		return array(
			'id'     => 'tadanero_car_related_tab',
			'title'  => __( 'Car related', 'essential-real-estate-tadanero' ),
			'icon'   => 'dashicons dashicons-car',
			'fields' => $this->get_fields_by_group( 'car-related', $fields ),
		);
	}

	/**
	 * Get fields without group assignment
	 *
	 * Returns all fields that don't have a group assignment.
	 *
	 * @since 1.0.0
	 * @return array Array of fields without group
	 */
	private function add_other() {
		return $this->get_fields_without_group();
	}

	/**
	 * Get fields without group assignment
	 *
	 * Filters and returns fields that don't have a group assignment.
	 *
	 * @since 1.0.0
	 * @return array Array of fields without group
	 */
	private function get_fields_without_group() {
		$fields         = $this->get_fields();
		$filtered_fields = array();

		foreach ( $fields as $field ) {
			if ( isset( $field['group'] ) ) {
				continue;
			}
			$filtered_fields[] = $field;
		}
		return $filtered_fields;
	}

	/**
	 * Get fields by group assignment
	 *
	 * Filters and returns fields that belong to a specific group,
	 * organizing them into rows of 2 columns each.
	 *
	 * @since 1.0.0
	 * @param string $group Group name to filter by
	 * @param array  $default_fields Default fields to merge with
	 * @return array Array of filtered fields organized in rows
	 */
	private function get_fields_by_group( $group, $default_fields = array() ) {
		$fields         = wp_parse_args( $this->get_fields(), $default_fields );
		$i              = 0;
		$filtered_fields = array();
		$one            = null;
		foreach ( $fields as $field ) {
			if ( ! isset( $field['group'] ) ) {
				continue;
			}
			if ( $field['group'] !== $group ) {
				continue;
			}
			/**
			 * heading
			 */
			if ( isset( $field['type'] ) && $field['type'] === 'heading' ) {
				if ( $one ) {
					$filtered_fields[] = $one;
					$one = null;
				}
				$filtered_fields[] = $field;
				$i = 0;
				continue;
			}
			/**
			 * force split
			 */
			if ( isset( $field['split'] ) && $field['split'] === 'before' ) {
				$i = 0;
				if ( $one ) {
					$filtered_fields[] = $one;
					$one = null;
				}
			}
			if ( 0 === $i % 2 ) {
				$one = array(
					'type'   => 'row',
					'col'    => 6,
					'fields' => array(),
				);
			}
			$one['fields'][] = $field;
			$i++;
			if ( 0 === $i % 2 ) {
				$filtered_fields[] = $one;
				$one            = null;
			}
		}
		if ( $one ) {
			$filtered_fields[] = $one;
		}
		return $filtered_fields;
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
		$measurement_unit           = ere_get_measurement_units();
		$measurement_units_land_area = ere_get_measurement_units_land_area();
		$dec_point                  = ere_get_option( 'decimal_point', '.' );
		$format_number              = '^[0-9]+([' . $dec_point . '][0-9]+)?$';
		$fields = array(
			/**
			 * Building
			 */
			array(
				'id'      => ERE_METABOX_PREFIX . 'building_area',
				'title'   => sprintf( __( 'Building Area (%s)', 'essential-real-estate-tadanero' ), $measurement_unit ),
				'title_frontend'   => __( 'Building Area', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: 50', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'building',
				'suffix' => $measurement_unit,
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'building_type',
				'title'   => __( 'Building Type', 'essential-real-estate-tadanero' ),
				'type'    => 'select',
				'options' => array(
					'detached-house'        => __( 'Detached house', 'essential-real-estate-tadanero' ),
					'terraced-house'        => __( 'Terraced house', 'essential-real-estate-tadanero' ),
					'semi-detached-house'   => __( 'Semi-detached house', 'essential-real-estate-tadanero' ),
					'townhouse'             => __( 'Townhouse', 'essential-real-estate-tadanero' ),
					'multi-family-building' => __( 'Multi-family building', 'essential-real-estate-tadanero' ),
					'farm'                  => __( 'Farm', 'essential-real-estate-tadanero' ),
					'habitat'               => __( 'Habitat', 'essential-real-estate-tadanero' ),
					'summer-house'          => __( 'Summer house', 'essential-real-estate-tadanero' ),
					'manor-house'           => __( 'Manor house', 'essential-real-estate-tadanero' ),
					'other'                 => __( 'Other', 'essential-real-estate-tadanero' ),
				),
				'group'   => 'building',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'building_material',
				'title'   => __( 'Building Material', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: concrete/brick', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'group'   => 'building',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'roof_type',
				'title'   => __( 'Roof Type', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: asphalt/tiles', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'group'   => 'building',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'roof_material',
				'title'   => __( 'Roof Material', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: asphalt/tiles', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'group'   => 'building',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'basement',
				'title'   => __( 'Basement', 'essential-real-estate-tadanero' ),
				'type'    => 'button_set',
				'default' => 'no',
				'group'   => 'building',
				'options' => array(
					'yes' => __( 'Yes', 'essential-real-estate-tadanero' ),
					'no'  => __( 'No', 'essential-real-estate-tadanero' ),
				),
				'frontend' => 'do-not-process',
				'split' => 'before',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'basement_area',
				'title'   => sprintf( __( 'Basement Area (%s)', 'essential-real-estate-tadanero' ), $measurement_unit ),
				'title_frontend'   => __( 'Basement Area', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: 50%s', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'building',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'basement',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => $measurement_unit,
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'attic',
				'title'   => __( 'Attic', 'essential-real-estate-tadanero' ),
				'type'    => 'button_set',
				'default' => 'no',
				'group'   => 'building',
				'options' => array(
					'yes' => __( 'Yes', 'essential-real-estate-tadanero' ),
					'no'  => __( 'No', 'essential-real-estate-tadanero' ),
				),
				'frontend' => 'do-not-process',
				'split' => 'before',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'attic_area',
				'title'   => sprintf( __( 'Attic Area (%s)', 'essential-real-estate-tadanero' ), $measurement_unit ),
				'title_frontend'   => __( 'Attic Area', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: 50', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'building',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'attic',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => $measurement_unit,
			),
			/**
			 * costs
			 */
			array(
				'id'      => ERE_METABOX_PREFIX . 'rent_monthly',
				'title'   => __( 'Monthly Rent', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: 4200', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'pattern'      => "{$format_number}", 
				'subtype' => 'money',
				'group'   => 'costs',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'other_fees',
				'title'   => __( 'Other Fees', 'essential-real-estate-tadanero' ),
				'desc'    => __( 'Example value: 500', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'pattern'      => "{$format_number}", 
				'subtype' => 'money',
				'group'   => 'costs',
			),
			/**
			 * media & energy: Energy Certificate
			 */
			array(
				'title'   => __( 'Energy Certificate', 'essential-real-estate-tadanero' ),
				'type'    => 'heading',
				'group'   => 'media',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'energy_certificate',
				'title'   => __( 'Energy Certificate', 'essential-real-estate-tadanero' ),
				'type'    => 'button_set',
				'default' => 'no',
				'group'   => 'media',
				'options' => array(
					'yes' => __( 'I have', 'essential-real-estate-tadanero' ),
					'no'  => __( 'I do not have', 'essential-real-estate-tadanero' ),
				),
				'frontend' => 'do-not-process',
				'split' => 'before',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'certificate_number',
				'title'   => __( 'Certificate number', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'media',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'energy_certificate',
						'1' => '=',
						'2' => 'yes',
					),
				),
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'useful_energy_demand_eu',
				'title'   => __( 'Useful energy demand - EU', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'media',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'energy_certificate',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => esc_html__( 'kWh/(m²·year)', 'essential-real-estate-tadanero' ),
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'final_energy_demand_ek',
				'title'   => __( 'Final energy demand - EK', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'media',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'energy_certificate',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => esc_html__( 'kWh/(m²·year)', 'essential-real-estate-tadanero' ),
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'non_renewable_primary_energy_demand_ep',
				'title'   => __( 'Non-renewable primary energy demand - EP', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'media',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'energy_certificate',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => esc_html__( 'kWh/(m²·year)', 'essential-real-estate-tadanero' ),
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'useful_energy_demand_eu',
				'title'   => __( 'Unit CO₂ emissions E<sub>CO2</sub>', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'media',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'energy_certificate',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => esc_html__( 't CO₂/(m²·year)', 'essential-real-estate-tadanero' ),
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'useful_energy_demand_eu',
				'title'   => __( 'Share of renewable energy sources in the annual final energy demand - U<sub>OZE</sub>', 'essential-real-estate-tadanero' ),
				'type'    => 'text',
				'default' => '',
				'group'   => 'media',
				'required' => array(
					array(
						'0' => ERE_METABOX_PREFIX . 'energy_certificate',
						'1' => '=',
						'2' => 'yes',
					),
				),
				'suffix' => '%',
			),
			/**
			 * media & energy: Connectors
			 */
			array(
				'title'   => __( 'Connectors', 'essential-real-estate-tadanero' ),
				'type'    => 'heading',
				'group'   => 'media',
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'power_connector',
				'title'   => __( 'Power Connector', 'essential-real-estate-tadanero' ),
				'type'    => 'radio',
				'options' => array(
					'connected' => __( 'Connected', 'essential-real-estate-tadanero' ),
					'not-connected' => __( 'Not Connected', 'essential-real-estate-tadanero' ),
				),
				'group'   => 'media',
				'value_inline' => false,
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'water_supply',
				'title'   => __( 'Water Supply', 'essential-real-estate-tadanero' ),
				'type'    => 'radio',
				'options' => array(
					'connected' => __( 'Connected', 'essential-real-estate-tadanero' ),
					'not-connected' => __( 'Not Connected', 'essential-real-estate-tadanero' ),
					'owned-well' => __( 'Owned Well', 'essential-real-estate-tadanero' ),
				),
				'group'   => 'media',
				'value_inline' => false,
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'sewerage_system',
				'title'   => __( 'Sewerage System', 'essential-real-estate-tadanero' ),
				'type'    => 'radio',
				'options' => array(
					'sewage-system' => __( 'Sewage system', 'essential-real-estate-tadanero' ),
					'no-sewage-system' => __( 'No sewage system', 'essential-real-estate-tadanero' ),
					'cesspool' => __( 'Cesspool', 'essential-real-estate-tadanero' ),
					'home-sewage-treatment-plant' => __( 'Home sewage treatment plant', 'essential-real-estate-tadanero' ),
				),
				'group'   => 'media',
				'value_inline' => false,
			),
			array(
				'id'      => ERE_METABOX_PREFIX . 'natural_gas',
				'title'   => __( 'Natural Gas', 'essential-real-estate-tadanero' ),
				'type'    => 'radio',
				'options' => array(
					'connected' => __( 'Connected', 'essential-real-estate-tadanero' ),
					'not-connected' => __( 'Not Connected', 'essential-real-estate-tadanero' ),
				),
				'group'   => 'media',
				'value_inline' => false,
			),


		/**
		 * other
		 */
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
					array(
						'id'      => ERE_METABOX_PREFIX . 'number_of_floors',
						'title'   => __( 'Number of Floors', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: 1/2/3', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
						'pattern'      => "{$format_number}", 
					),
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
					array(
						'id'      => ERE_METABOX_PREFIX . 'floor_type',
						'title'   => __( 'Floor Type', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: wood/tiles', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'height',
						'title'   => __( 'Height', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: 250', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
						'pattern'      => "{$format_number}", 
					),
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
					array(
						'id'      => ERE_METABOX_PREFIX . 'kitchen_type',
						'title'   => __( 'Kitchen', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: modern/classic', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'bathroom_type',
						'title'   => __( 'Bathroom', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: modern/classic', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'heating_source',
						'title'   => __( 'Heating Source', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: heatpump/gas/electric', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'heating_system',
						'title'   => __( 'Heating System', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: central/individual', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'storage_unit',
						'title'   => __( 'Storage Unit', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: yes/no/10m²', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'plot_type',
						'title'   => __( 'Plot Type', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: residential/commercial', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'plot_access',
						'title'   => __( 'Plot Access', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: paved/unpaved', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'default' => '',
					),

					/**
					 * garage
					 */
					array(
						'id'      => ERE_METABOX_PREFIX . 'garage_type',
						'title'   => __( 'Garage Type', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: detached/attached/other', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'group'   => 'car-related',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'parking_type',
						'title'   => __( 'Parking Type', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: garage/carport', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'group'   => 'car-related',
					),
					array(
						'id'      => ERE_METABOX_PREFIX . 'parking_spaces',
						'title'   => __( 'Parking Spaces', 'essential-real-estate-tadanero' ),
						'desc'    => __( 'Example value: 1/2/3', 'essential-real-estate-tadanero' ),
						'type'    => 'text',
						'group'   => 'car-related',
						'pattern'      => "{$format_number}", 
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
		$additional_fields = $this->get_fields();

		// Process each additional field
		foreach ( $additional_fields as $field ) {
			// Skip fields that should not be processed in frontend
			if ( isset( $field['frontend'] ) && 'do-not-process' === $field['frontend'] ) {
				continue;
			}

			$property_field        = get_post_meta( $property_id, $field['id'], true );
			$property_field_content = $property_field;

			/**
			 * Handle money subtype
			 */
			if ( isset( $field['subtype'] ) ) {
				switch ( $field['subtype'] ) {
					case 'money':
						$property_field_content = ere_get_format_money( $property_field );
						break;
					case 'heading':
						// Do nothing, just skip
						continue 2;
				}
			}

			/**
			 * Handle field type
			 */
			switch ( $field['type'] ) {
				case 'radio':
				case 'select':
					if ( is_array( $field['options'] ) && isset( $field['options'][$property_field] ) ) {
						$property_field_content = esc_html( $field['options'][$property_field] );
					}
					break;
				case 'checkbox_list':
					// Handle checkbox_list field type
					$property_field_content = '';
					if ( is_array( $property_field ) ) {
						foreach ( $property_field as $value => $v ) {
							$property_field_content .= $v . ', ';
						}
						$property_field_content = rtrim( $property_field_content, ', ' );
					}
					break;
				case 'textarea':
					// Handle textarea field type
					$property_field_content = wpautop( $property_field_content );
					break;
			}

			// Add field to overview if it has content
			if ( ! empty( $property_field_content ) ) {
				$title  = isset( $field['title_frontend'] ) ? $field['title_frontend'] : $field['title'];
				$suffix = isset( $field['suffix'] ) ? ' ' . $field['suffix'] . '' : '';

				$fields[ $field['id'] ] = array(
					'title'    => $title,
					'content'  => '<span>' . $property_field_content . $suffix . '</span>',
					'priority' => $priority,
				);
			}

			$priority += 10;
		}

		return $fields;
	}
}
