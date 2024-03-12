<?php
/**
 * Retrieves the total amount spent by a user based on their previous purchases.
 *
 * @param int $user_id The ID of the user for whom the total spent is to be retrieved.
 * @return float The total amount spent by the user. Returns 0 if no value is found.
 */
function bloopanimation_get_previous_purchases_value( $user_id ) {

    global $wpdb;

    // Check Membpress Spending
	$membpress_spending = $wpdb->get_var($wpdb->prepare(
	    "SELECT SUM(total) FROM {$wpdb->prefix}mepr_transactions WHERE user_id = %d",
	    $user_id
	));
	$membpress_spending = ( $membpress_spending !== null ) ? $membpress_spending : 0;

	// Check Easy Digital Downloads Spending
	$edd_spending = $wpdb->get_var($wpdb->prepare(
	    "SELECT SUM(total) FROM {$wpdb->prefix}edd_orders WHERE user_id = %d",
	    $user_id
	));
	$edd_spending = ( $edd_spending !== null ) ? $edd_spending : 0;

	$total = ( $membpress_spending + $edd_spending );

	return $total;
}