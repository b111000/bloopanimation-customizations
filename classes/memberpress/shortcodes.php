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
    function add_to_cart_button() {

        if ( !isset( $_SESSION['bloopanimation-in-cart-product-id'] ) ) {
            return '';
        }

        $product_id = $_SESSION['bloopanimation-in-cart-product-id'];

        ob_start();
        ?>
        <div class="bloopanimation-header-cart">
            <a href="<?php echo get_the_permalink( $product_id ); ?>">
                <i class="wpmenucart-icon-shopping-cart-0" ></i>
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
