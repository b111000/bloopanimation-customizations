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

// add_filter( 'mepr_transaction_product', 'bloopanimation_set_membership_price', 900, 1 );
// function bloopanimation_set_membership_price( $product_obj ) {
//     // print "<pre>";
// 	// print_r( $product_obj );
// 	// print "</pre>";
// 	$product_obj->price = 18.990;
// 	return $product_obj;
// }

// add_filter( 'mepr_display_invoice_txn', 'bloopanimation_display_invoice_txn', 900, 1 );
// function bloopanimation_display_invoice_txn( $tmp_txn ) {
// 	$tmp_txn->amount = 19.990;
// 	$tmp_txn->total  = 23.990;

//     // print "<pre>";
// 	// print_r( $tmp_txn );
// 	// print "</pre>";

// 	return $tmp_txn;
// }


return;

add_filter( 'mepr-price-string', 'bloopanimation_display_invoice_txn', 900, 3 );
function bloopanimation_display_invoice_txn( $price_str, $obj, $show_symbol ) {
	if ( !isset( $_GET['coupon'] ) ) {
		return $price_str;
	}
	if ( $_GET['coupon'] != 'Upgrade-Discount' ) {
		return $price_str;
	}
	$product   = new MeprProduct( $obj->product_id );
	$price_str = '$'.$product->price;
	return $price_str;
}


add_filter( 'gettext_memberpress', 'bloopanimation_change_checkout_labels', 900, 3 );
function bloopanimation_change_checkout_labels( $translation, $text, $domain ) {
	if ( $translation == "Using Coupon &ndash; %s" ) {
		$translation = 'Upgrade Discount';
	}elseif( $translation == "Coupon Code '%s'" ){
		$translation = 'Upgrade Discount';
	}
	return $translation;
}


/**
 * Retrieves the total amount spent by a user based on their previous purchases.
 *
 * @param int $user_id The ID of the user for whom the total spent is to be retrieved.
 * @return float The total amount spent by the user. Returns 0 if no value is found.
 */
function bloopanimation_get_revious_purchases_value( $user_id ) {

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
	$discount_amount = bloopanimation_get_revious_purchases_value( $user_id );

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
	$discount_amount = bloopanimation_get_revious_purchases_value( $user_id );

	return $discount_amount;
}
