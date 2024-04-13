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
                                <strong>
                                    <?php esc_html_e( 'Transaction ID', 'bloopanimation' ); ?>:
                                    <?php echo esc_html( $purchase['trans_num'] ); ?>
                                </strong><br>
                                <div>
                                    <?php esc_html_e( 'Date', 'bloopanimation' ); ?>:
                                    <?php echo esc_html( date_i18n('F, jS, Y', strtotime( $purchase['created_at'] )) ); ?>
                                </div>
                                <div>
                                    <?php esc_html_e( 'Total', 'bloopanimation' ); ?>:
                                    $<?php echo esc_html( $purchase['total'] ); ?>
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
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Shortcodes_Object = new BACU_BloopAnimation_Customizations_Memberpress_Shortcodes();
