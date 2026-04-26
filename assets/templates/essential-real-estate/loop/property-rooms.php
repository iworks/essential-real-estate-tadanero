<?php
// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}
/**
 * @var $property_rooms
 */
?>
<div class="ere__loop-property-info-item property-rooms" data-toggle="tooltip" title="<?php /* translators: %s: Number of property rooms. */ echo esc_attr(sprintf( _n( '%s room', '%s rooms', $property_rooms, 'essential-real-estate-tadanero' ), $property_rooms )); ?>">
    <i class="fa fa-hotel"></i>
    <div class="ere__lpi-content">
        <span class="ere__lpi-value"><?php echo esc_html( $property_rooms ) ?></span>
        <span class="ere__lpi-label"><?php echo esc_html(_n( 'Room', 'Rooms', $property_rooms, 'essential-real-estate-tadanero' )) ?></span>
    </div>
</div>
