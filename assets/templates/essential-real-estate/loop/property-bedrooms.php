<?php
// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}
/**
 * @var $property_bedrooms
 */
?>
<div class="ere__loop-property-info-item property-bedrooms">
    <i class="fa fa-hotel"></i>
    <div class="ere__lpi-content">
        <span class="ere__lpi-value"><?php echo esc_html( $property_bedrooms ) ?></span>
        <span class="ere__lpi-label"><?php echo esc_html(_n( 'Bedroom', 'Bedrooms', $property_bedrooms, 'essential-real-estate' )) ?></span>
    </div>
</div>