<?php 
class BACU_BloopAnimation_Customizations_Groundhogg_Shortcodes {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_shortcode( 'bloopanimation_abandoned_cart_recovery_link', array( $this, 'get_memberpress_product_link' ), 10, 1 );
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
}//End of class

/*      
 * Object
 *
 */
$BACU_Groundhogg_Shortcodes_Object = new BACU_BloopAnimation_Customizations_Groundhogg_Shortcodes();
