<?php 
class BACU_BloopAnimation_Customizations_Memberpress_Shortcodes {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_shortcode( 'bloopanimation_header_cart', array( $this, 'add_to_cart_button' ), 10, 1 );
    }

     /**
     * Show add to cart link or button
     * 
     */ 
    function add_to_cart_button( $atts ) {

        if ( !isset( $_SESSION['bloopanimation-in-cart-product-id'] ) ) {
            return '';
        }

        $product_id = $_SESSION['bloopanimation-in-cart-product-id'];

        ob_start();
        ?>
        <div class="bloopanimation-header-cart">
            <a href="<?php echo get_the_permalink( $product_id ); ?>">
                <img src="<?php echo esc_url( plugins_url( 'assets/icons/svg/shopping-cart.svg', dirname(__DIR__) ) ); ?>" alt="<?php esc_attr_e( 'Cart icon', 'bloopanimation' ); ?>">
                <div class="bloopanimation-bubble">1</div>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Shortcodes_Object = new BACU_BloopAnimation_Customizations_Memberpress_Shortcodes();
