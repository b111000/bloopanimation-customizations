<?php
/*
 * Plugin Name: Bloopanimation - Customizations
 * Description: [When people purchase a certain Memberpress membership, the price is discounted by all previous purchases]
 * Author: William
 * Version: 1.0.0
 * Author URI: https://app.codeable.io/tasks/new?preferredContractor=77368
 * Text Domain: bloopanimation
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Classes
 */
include( plugin_dir_path( __FILE__ ) . 'classes/memberpress/checkout.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/memberpress/meta-fields.php' );
include( plugin_dir_path( __FILE__ ) . 'classes/assets/assets.php' );



/**
 * Retrieves the total amount spent by a user based on their previous purchases.
 *
 * @param int $user_id The ID of the user for whom the total spent is to be retrieved.
 * @return float The total amount spent by the user. Returns 0 if no value is found.
 */
function bloopanimation_get_previous_purchases_value( $user_id ) {

    global $wpdb;

	$result = $wpdb->get_var($wpdb->prepare(
	    "SELECT SUM(total) FROM {$wpdb->prefix}mepr_transactions WHERE user_id = %d",
	    $user_id
	));

	return ($result !== null) ? $result : 0;
}

/**
 * Set a default MemberPress coupon on initialization if certain conditions are met.
 */
add_action( 'init', 'bloopanimation_set_memberpress_coupon' );
function bloopanimation_set_memberpress_coupon() {

	if ( !is_user_logged_in() ) {
		return;
	}

	if ( is_admin() ) {
		return;
	}

	if ( isset( $_GET['coupon'] ) ) {
		return;
	}

	$user_id         = get_current_user_id();
	$discount_amount = bloopanimation_get_previous_purchases_value( $user_id );

	if ( empty( $discount_amount ) || $discount_amount == 0 ) {
		return;
	}

	$_GET['coupon'] = 'Upgrade-Discount';
}

/**
 * Hooking into the 'mepr_coupon_get_discount_amount' filter to customize
 * the discount amount for MemberPress coupons based on user's previous purchases.
 */
add_filter( 'mepr_coupon_get_discount_amount', 'bloopanimation_set_memberpress_coupon_amount', 900, 3 );
function bloopanimation_set_memberpress_coupon_amount( $discount_amount, $obj, $prd ) {

	if ( !is_user_logged_in() ) {
		return $discount_amount;
	}

	$user_id         = get_current_user_id();
	$discount_amount = bloopanimation_get_previous_purchases_value( $user_id );

	return $discount_amount;
}

/**
 * Modify the translated coupon code text in MemberPress.
 *
 * This function is hooked into the 'gettext_memberpress' filter of the MemberPress plugin
 * and is responsible for changing the displayed coupon code text when a specific coupon is applied.
 *
 * @param string $translation The original translated text.
 * @param string $text The original text before translation.
 * @param string $domain The text domain.
 * @return string The modified translated text.
 */
add_filter( 'gettext_memberpress', 'bloopanimation_change_coupon_code_text', 900, 3 );
function bloopanimation_change_coupon_code_text( $translation, $text, $domain ) {

	if ( is_admin() ) {
		return $translation;
	}

	if ( !isset( $_GET['coupon'] ) ) {
		return $translation;
	}

	if ( $_GET['coupon'] != 'Upgrade-Discount' ) {
		return $translation;
	}

	if ( $translation == "Using Coupon &ndash; %s" ) {
		$translation = 'Upgrade Discount';
	}elseif( $translation == "Coupon Code '%s'" ){
		$translation = 'Upgrade Discount';
	}
	return $translation;
}

/**
 * Enqueue custom CSS styles in the <head> section of the WordPress website.
 *
 * This function is hooked to the 'wp_head' action to add custom CSS styles
 *
 * @return void
 */
add_action( 'wp_head', 'bloopanimation_header_css_styles' );
function bloopanimation_header_css_styles() {

	if ( !is_user_logged_in() ) {
		return;
	}

	if ( is_admin() ) {
		return;
	}

	if ( !isset( $_GET['coupon'] ) ) {
		return;
	}

	if ( $_GET['coupon'] != 'Upgrade-Discount' ) {
		return;
	}

	?>
	<style type="text/css">
		.mepr-checkout-container.mp_wrapper .mp-table tr td .desc {
			display: none;
		}
	</style>
	<?php
}

/**
 * Filter callback to modify the 'mepr-invoice' data.
 *
 * This function is hooked to the 'mepr-invoice' filter and is used to remove the
 * " – Payment" suffix from the 'description' field of each item in the 'items' array.
 *
 * @param array $invoice The original invoice data.
 * @param object $txn The transaction object.
 * @return array Modified invoice data with 'description' field in 'items' array updated.
 */
add_filter('mepr-invoice', 'bloopanimation_remove_payment_txt_from_item', 900, 2 );
function bloopanimation_remove_payment_txt_from_item( $invoice, $txn ) {
	
	if ( is_admin() ) {
		return $invoice;
	}

    // Check if 'items' array exists in the $invoice
    if (isset($invoice['items']) && is_array($invoice['items'])) {
        // Loop through each item and modify the 'description' field
        foreach ($invoice['items'] as &$item) {
            if (isset($item['description'])) {
                $item['description'] = str_replace('&nbsp;&ndash;&nbsp;Payment', '', $item['description']);
            }
        }
    }
    return $invoice;
}

