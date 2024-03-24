<?php 
class BACU_BloopAnimation_Customizations_Groundhogg {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action( 'wp_ajax_nopriv_bloopanimation_groundhogg_process_contact', array( $this, 'process_contact' ) );
        add_action( 'wp_ajax_bloopanimation_groundhogg_process_contact', array( $this, 'process_contact' ) );
        add_action( 'template_redirect', array( $this, 'process_contact_logged_in' ) );
        add_action( 'bloopanimation_groundhogg_process_contact', array( $this, 'start_benchmark' ), 10, 1 );
        add_action( 'template_redirect', array( $this, 'redirect' ) );
        add_action( 'mepr-txn-store', array( $this, 'delete_memberpress_pending_purchase' ), 10, 2 );
        add_action( 'mepr-txn-store', array( $this, 'tag_user' ), 10, 2 );
        add_action( 'bloopanimation_groundhogg_cart_recovered', array( $this, 'stop_funnel' ), 10, 1 );
    }

    /**
     * Save memberpress pending purchase
     * @link https://help.groundhogg.io/article/201-adding-a-tag-on-an-action
     */ 
    function save_memberpress_pending_purchase( $email, $product_id, $first_name='', $last_name='' ) {

        // This will retrieve any existing contact or maken a new one
        $contact = new \Groundhogg\Contact( [
            'first_name' => $first_name, 
            'last_name'  => $last_name,    
            'email'      => $email,
        ] );

        // Don't add contact to the funnel for same product if they've already been added
        $memberpress_info = $contact->get_meta( '_bloopanimation_memberpress_pending_purchase' );
        if ( isset( $memberpress_info['memberpress_product_id'] ) && $memberpress_info['memberpress_product_id'] == $product_id  ) {
            return;
        }

        // Update the contact
        $contact->update_meta( 
            '_bloopanimation_memberpress_pending_purchase', 
            array( 
                'memberpress_product_id' => $product_id,
            ) 
        );

        // Add them to the funnel
        do_action( 'bloopanimation_groundhogg_process_contact', $email );
    }

    /**
     * Process Contact
     */ 
    function process_contact() {
        // Check AJAX request
        check_ajax_referer( 'bloopanimation_nonce', 'ajax_nonce' );

        // Don't proceed if required class doesn't exist
        if ( !class_exists( '\Groundhogg\Contact' ) ) {
            return;
        }

        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name  = sanitize_text_field( $_POST['last_name'] );
        $email      = sanitize_text_field( $_POST['email'] );
        $product_id = sanitize_text_field( $_POST['product_id'] );

        if ( !is_email( $email ) ) {
            wp_send_json_error( [] );
        }

        // Save memberpress pending purchase
        $this->save_memberpress_pending_purchase( $email, $product_id, $first_name, $last_name );

        // Send back response
        wp_send_json( [] );
    }

    /**
     * Process Contact for logged in users
     */ 
    function process_contact_logged_in() {

        if ( !is_user_logged_in() ) {
            return;
        }

        if ( !isset( $_POST['mepr_product_id'] ) ) {
            global $post;

            if ( $post == null || !isset( $post->ID ) ) {
                return;
            }

            $product_id = $post->ID;
        }else {
            $product_id = sanitize_text_field( $_POST['mepr_product_id'] );
        }

        if ( get_post_type( $product_id ) != 'memberpressproduct' ) {
            return;
        }

        // Save memberpress pending purchase
        $user_id    = get_current_user_id();
        $user_info  = get_userdata( $user_id );
        $email      = $user_info->user_email;
        $first_name = $user_info->first_name;
        $last_name  = $user_info->last_name;
        $this->save_memberpress_pending_purchase( $email, $product_id, $first_name, $last_name );
    }

    /**
     * Add user to funnel
     * Start benchmark
     * 
     */ 
    function start_benchmark( $email ) {

        $contact = new \Groundhogg\Contact( [ 
            'email' => $email
        ] );

        \Groundhogg\do_plugin_api_benchmark( 'bloopanimation_memberpress_cart_is_abandoned', $contact->get_id() );
    }

    /**
     * Redirect to product page
     * 
     */ 
    function redirect() {

        // Don't proceed if required class doesn't exist
        if ( !class_exists( '\Groundhogg\Contact' ) ) {
            return;
        }

        if ( !isset( $_GET['bloopanimation-recover-cart'] ) ) {
            return;
        }

        if ( !isset( $_GET['email'] ) || empty( $_GET['email'] ) ) {
            return;
        }

        $email   = sanitize_text_field( $_GET['email'] );
        $contact = new \Groundhogg\Contact( [  
            'email'=> $email,
        ] );

        $memberpress_info = $contact->get_meta( '_bloopanimation_memberpress_pending_purchase' );
        if ( !isset( $memberpress_info['memberpress_product_id'] ) || empty( $memberpress_info['memberpress_product_id'] ) ) {
            return;
        }

        $product_id = sanitize_text_field( $memberpress_info['memberpress_product_id'] );

        wp_redirect( get_the_permalink( $product_id ) );
        exit;
    }

    /**
     * Redirect to product page
     * 
     */
    function delete_memberpress_pending_purchase( $txn, $old_txn ) {

        // Don't proceed if required class doesn't exist
        if ( !class_exists( '\Groundhogg\Contact' ) ) {
            return;
        }

        if ( trim( $txn->status ) !== 'complete' ) {
            return;
        }

        if ( !is_user_logged_in() ) {
            return;
        }

        $user_id   = get_current_user_id();
        $user_info = get_userdata( $user_id );
        $email     = $user_info->user_email;

        $contact = new \Groundhogg\Contact( [  
            'email'=> $email,
        ] );

        $contact->delete_meta( '_bloopanimation_memberpress_pending_purchase' );

        // Remove them from the funnel
        do_action( 'bloopanimation_groundhogg_cart_recovered', $email );
    }

    /**
     * Redirect to product page
     * 
     */
    function tag_user( $txn, $old_txn ) {

        // Don't proceed if required class doesn't exist
        if ( !class_exists( '\Groundhogg\Contact' ) ) {
            return;
        }

        if ( trim( $txn->status ) !== 'complete' ) {
            return;
        }

        if ( !is_user_logged_in() ) {
            return;
        }

        $user_id   = get_current_user_id();
        $user_info = get_userdata( $user_id );
        $email     = $user_info->user_email;

        $contact = new \Groundhogg\Contact( [  
            'email'=> $email,
        ] );

        $purchases_so_far = floatval( bloopanimation_get_previous_purchases_value( $user_id ) );
        $ltv_value        = 250;
        $ltv_value        = apply_filters( 'bloopanimation-customer-ltv', $ltv_value );

        $tags_to_add = array(
            'Sale is Over $'.$ltv_value,
        );

        if ( $purchases_so_far >= $ltv_value ) {
            $contact->apply_tag( $tags_to_add );
        }
    }

    /**
     * At this point, the cart has been recovered. 
     * Stop the funnel
     * 
     */
    function stop_funnel( $email ) {
        \Groundhogg\do_plugin_api_benchmark( 'bloopanimation_memberpress_sale_was_made', $email, false );
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Groundhogg_Object = new BACU_BloopAnimation_Customizations_Groundhogg();
