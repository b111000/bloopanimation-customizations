<?php 
class BACU_BloopAnimation_Customizations_Groundhogg_Shortcodes {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_shortcode( 'bloopanimation_abandoned_cart_recovery_link', array( $this, 'get_memberpress_product_link' ), 10, 1 );
        add_shortcode( 'bloopanimation_abandoned_cart_product_image', array( $this, 'get_memberpress_product_image' ), 10, 1 );
    }

    /**
     * Get memberpress product link
     */ 
    function get_memberpress_product_link( $atts ) {

        $args = [];
        $args = shortcode_atts( 
            array(
                'email' => '',
            ), 
            $atts
        );

        return home_url( '/?bloopanimation-recover-cart&email='.esc_html( $args['email'] ) );
    }

    /**
     * Get memberpress product image
     */ 
    function get_memberpress_product_image( $atts ) {

        // Don't proceed if required class doesn't exist
        if ( !class_exists( '\Groundhogg\Contact' ) ) {
            return;
        }

        $args = [];
        $args = shortcode_atts( 
            array(
                'email' => '',
            ), 
            $atts
        );

        $email   = sanitize_text_field( $args['email'] );
        $contact = new \Groundhogg\Contact( [  
            'email'=> $email,
        ] );

        $memberpress_info = $contact->get_meta( '_bloopanimation_memberpress_pending_purchase' );
        if ( !isset( $memberpress_info['memberpress_product_id'] ) || empty( $memberpress_info['memberpress_product_id'] ) ) {
            return;
        }

        $product_id         = sanitize_text_field( $memberpress_info['memberpress_product_id'] );
        $featured_image_url = get_the_post_thumbnail_url( $product_id, 'thumbnail' );

        if ( empty( $featured_image_url ) ) {
            return;
        }

        ob_start();
        ?>
            <img src="<?php echo esc_url( $featured_image_url ); ?>" alt="<?php esc_html_e( 'Product Image', 'bloopanimation' ); ?>">
        <?php 

        return ob_get_clean();
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Groundhogg_Shortcodes_Object = new BACU_BloopAnimation_Customizations_Groundhogg_Shortcodes();
