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
	$result = $wpdb->get_var($wpdb->prepare(
	    "SELECT SUM(total) FROM {$wpdb->prefix}mepr_transactions WHERE user_id = %d",
	    $user_id
	));

	if ( !empty( $result ) || $result !== null ) {
		return $result;
	}

	// Check Easy Digital Downloads Spending
	$result = $wpdb->get_var($wpdb->prepare(
	    "SELECT SUM(total) FROM {$wpdb->prefix}edd_orders WHERE user_id = %d",
	    $user_id
	));

	return ($result !== null) ? $result : 0;
}