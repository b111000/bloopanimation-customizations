<?php 
class BACU_BloopAnimation_Customizations_Memberpress_Checkout {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_filter( 'mepr-price-string', array( $this, 'replace_with_coupon_text_with_html' ), 900, 3 );
    }

    /**
     * Modify the display of membership price string and return html instead
     *
     *
     * @param string $price_str The original price string.
     * @param object $obj The MeprProduct object representing the product.
     * @param bool $show_symbol Flag indicating whether to show the currency symbol.
     * @return string The modified price string.
     */
    function replace_with_coupon_text_with_html( $price_str, $obj, $show_symbol ) {

        if ( is_admin() ) {
            return $price_str;
        }

        global $wpdb;

        $post_id      = $obj->product_id;
        $meta_key     = 'bloopanimation-mepr-checkout-txt';
        $checkout_txt = get_post_meta( $post_id, 'bloopanimation-mepr-checkout-txt', true );

        ob_start();
        ?> 
            <div class="bloopanimation-logo-n-text">
                <img src="<?php echo esc_url( plugins_url( 'assets/images/bloopAnimation_logo.png', dirname(__DIR__) ) ); ?>" alt="logo">
                <?php if ( !empty( $checkout_txt ) ): ?>
                    <p>
                        <?php echo esc_html( $checkout_txt ); ?>
                    </p>
                <?php endif ?>
            </div>
        <?php
        $price_str = ob_get_clean();

        return $price_str;
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Checkout_Object = new BACU_BloopAnimation_Customizations_Memberpress_Checkout();
