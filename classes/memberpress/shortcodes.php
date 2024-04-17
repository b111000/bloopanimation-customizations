<?php 

use function Groundhogg\get_contactdata;

class BACU_BloopAnimation_Customizations_Memberpress_Shortcodes {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_shortcode( 'bloopanimation_header_cart', array( $this, 'add_to_cart_button' ), 10, 1 );
        add_shortcode( 'bloopanimation_list_purchases', array( $this, 'list_purchases' ), 10, 1 );
        add_shortcode( 'bloopanimation_mepr_percent_discount', array( $this, 'generates_percent_discount' ), 10, 1 );
    }

    /**
     * Show add to cart link or button
     * 
     */ 
    function add_to_cart_button( $atts ) {

        if ( !isset( $_COOKIE['bloopanimation-product-in-the-cart'] ) ) {
            return '';
        }

        $product_id = $_COOKIE['bloopanimation-product-in-the-cart'];

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

        if ( !is_user_logged_in() ) {
            return __( 'Please login first.', 'bloopanimation' );
        }

        ob_start();

        ?>
        <div class="bloopanimation-purchases-list-wrapper">

            <?php

            global $wpdb;

            $user_id   = get_current_user_id();
            $purchases = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}mepr_transactions WHERE user_id = %d AND status = 'complete' ORDER BY id DESC",
                    $user_id
            ), ARRAY_A );

            ?>

            <?php if ( count( $purchases ) <= 0 ): ?>
                <h3>
                    <?php esc_html_e( 'No trasactions yet', 'bloopanimation'); ?>
                </h3>
            <?php endif ?>


            <?php if ( count( $purchases ) >= 1 ): ?>
                <?php foreach ( $purchases as $purchase ): ?>
                    <div class="bloopanimation-purchases-list-box">

                        <?php $product_id = $purchase['product_id']; ?>

                        <div class="bloopanimation-purchases-list-box-header">
                            <div>
                                <span>
                                    <?php esc_html_e( 'Transaction ID', 'bloopanimation' ); ?>:
                                    <?php echo esc_html( $purchase['trans_num'] ); ?>
                                </span><br>
                                <div>
                                    <?php echo esc_html( date_i18n('F, jS, Y', strtotime( $purchase['created_at'] )) ); ?>
                                    |
                                    <?php esc_html_e( 'Total', 'bloopanimation' ); ?>: $<?php echo esc_html( $purchase['total'] ); ?>
                                </div>
                            </div>
                            <div class="bloopanimation-left-flex-div">
                                <a href="
                                <?php
                                    echo MeprUtils::admin_url(
                                        'admin-ajax.php',
                                        array( 'download_invoice', 'mepr_invoices_nonce' ),
                                        array(
                                            'action' => 'mepr_download_invoice',
                                            'txn'    => $purchase['id'],
                                        )
                                    );
                                ?>
                                "
                                class="button"
                                target="_blank"
                                >
                                    <?php esc_html_e( 'Download Invoice', 'bloopanimation' ); ?>
                                </a>
                            </div>
                        </div>

                        <div class="bloopanimation-purchases-list-box-body">
                            <?php if ( has_post_thumbnail( $product_id ) ): ?>
                                <div>
                                    <?php echo wp_kses_post( get_the_post_thumbnail( $product_id, 'thumbnail' ) ); ?>
                                </div>
                            <?php endif ?>
                            <div class="bloopanimation-purchases-box-title">
                                <strong>
                                    <?php echo esc_html( get_the_title( $product_id ) ); ?>
                                </strong>
                            </div>
                        </div>

                    </div>
                <?php endforeach ?>
            <?php endif ?>


        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Generates a percentage discount coupon in MemberPress for a specified email address.
     *
     * This function creates a new percentage discount coupon in MemberPress and associates it with
     * a specific email address and optionally valid products.
     *
     * @param array $atts Shortcode attributes.
     *                   - email (string)      : The email address of the contact to apply the discount for.
     *                   - valid_products (string) : Comma-separated list of product IDs to restrict the coupon to (optional).
     * @return string|null The generated coupon code, or null if coupon creation fails or prerequisites are not met.
     */
    function generates_percent_discount( $atts ) {

        if ( !class_exists( 'MeprCoupon' ) ) {
            return;
        }

        if ( !function_exists( 'Groundhogg\get_contactdata' ) ) {
            return;
        }

        $args = [];
        $args = shortcode_atts( 
            array(
                'email'          => '',
                'valid_products' => '',
            ), 
            $atts
        );

        $contact = get_contactdata( $args['email'] );

        if ( !$contact ) {
            return;
        }

        // Don't generate coupons when updating/creating emails
        if ( strpos( $_SERVER['REQUEST_URI'], '/wp-json/gh/' ) == true ) {
            return;
        }

        $valid_products = [];
        if ( !empty( $args['valid_products'] ) ) {
            $valid_products = explode( ',', $args['valid_products'] );
        }

        $coupon_code             = strtoupper( bin2hex(openssl_random_pseudo_bytes(5)) );
        $coupon                  = new MeprCoupon();
        $coupon->post_title      = sanitize_text_field( $coupon_code );
        $coupon->discount_type   = 'percent';
        $coupon->discount_amount = 100;
        $coupon->usage_amount    = 1;
        $coupon->valid_products  = $valid_products;
        $coupon->save();

        return $coupon_code;
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Shortcodes_Object = new BACU_BloopAnimation_Customizations_Memberpress_Shortcodes();
