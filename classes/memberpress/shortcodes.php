<?php 
class BACU_BloopAnimation_Customizations_Memberpress_Shortcodes {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_shortcode( 'bloopanimation_header_cart', array( $this, 'add_to_cart_button' ), 10, 1 );
        add_shortcode( 'bloopanimation_list_purchases', array( $this, 'list_purchases' ), 10, 1 );
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

    /**
     * List purchases
     * 
     */ 
    function list_purchases( $atts ) {
        ob_start();
        ?>
        <div class="bloopanimation-purchases-list-wrapper">

            <div class="bloopanimation-purchases-list-box">

                <div class="bloopanimation-purchases-list-box-header">
                    <div>
                        <strong>
                            <?php esc_html_e( 'Order #', 'bloopanimation' ); ?>123456789
                        </strong><br>
                        <div>
                            <?php esc_html_e( 'Date', 'bloopanimation' ); ?>:
                            30th June, 2024
                        </div>
                        <div>
                            <?php esc_html_e( 'Total', 'bloopanimation' ); ?>:
                            $35.37
                        </div>
                    </div>
                    <div class="bloopanimation-left-flex-div">
                        <a href="" class="button">
                            <?php esc_html_e( 'Download Invoice', 'bloopanimation' ); ?>
                        </a>
                    </div>
                </div>

                <div class="bloopanimation-purchases-list-box-body">
                    <div>
                        <?php echo wp_kses_post( get_the_post_thumbnail( 11, 'medium' ) ); ?>
                    </div>
                    <div class="bloopanimation-purchases-box-title">
                        <strong>
                            <?php echo esc_html( get_the_title( 11 ) ); ?>
                        </strong>
                    </div>
                </div>

            </div>
            <div class="bloopanimation-purchases-list-box">

                <div class="bloopanimation-purchases-list-box-header">
                    <div>
                        <strong>
                            <?php esc_html_e( 'Order #', 'bloopanimation' ); ?>123456789
                        </strong><br>
                        <div>
                            <?php esc_html_e( 'Date', 'bloopanimation' ); ?>:
                            30th June, 2024
                        </div>
                        <div>
                            <?php esc_html_e( 'Total', 'bloopanimation' ); ?>:
                            $35.37
                        </div>
                    </div>
                    <div class="bloopanimation-left-flex-div">
                        <a href="" class="button">
                            <?php esc_html_e( 'Download Invoice', 'bloopanimation' ); ?>
                        </a>
                    </div>
                </div>

                <div class="bloopanimation-purchases-list-box-body">
                    <div>
                        <?php echo wp_kses_post( get_the_post_thumbnail( 11, 'medium' ) ); ?>
                    </div>
                    <div class="bloopanimation-purchases-box-title">
                        <strong>
                            <?php echo esc_html( get_the_title( 11 ) ); ?>
                        </strong>
                    </div>
                </div>

            </div>

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
