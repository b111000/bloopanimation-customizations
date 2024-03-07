<?php 
class BACU_BloopAnimation_Customizations_Memberpress_Checkout {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_filter( 'esc_html', array( $this, 'esc_html' ), 900, 2 );
        add_filter( 'gettext_with_context_memberpress', array( $this, 'change_pay_text' ), 900, 4 );
        add_filter( 'gettext_memberpress', array( $this, 'change_coupon_code_text' ), 900, 3 );
        add_filter( 'gettext_with_context_memberpress', array( $this, 'change_coupon_code_text_with_context' ), 900, 4 );
        add_filter( 'clean_url', array( $this, 'replace_with_featured_img_url' ), 900, 3 );
        add_filter( 'mepr_coupon_get_discount_amount', array( $this, 'set_memberpress_coupon_amount' ), 900, 3 );
        add_filter( 'mepr-invoice', array( $this, 'remove_payment_txt_from_item' ), 900, 2 );

        add_action( 'wp_print_styles', array( $this, 'header_css_styles_logged_in' ) );
        add_action( 'template_redirect', array( $this, 'set_memberpress_coupon' ) );
    }

    /**
     * We need to allow html otherwise it will be escaped for this string
     * See 'change_pay_text' function
     *
     */
    function esc_html( $safe_text, $text ) {

        if ( strpos( $text, 'bloopanimation-logo-n-text' ) == true ) {
            $safe_text = wp_kses_post( $text );
        }

        return $safe_text;
    }

    /**
     * Change 'Pay %s' text
     * 
     */
    function change_pay_text( $translation, $text, $context, $domain ) {

        if ( $translation != 'Pay %s' ) {
            return $translation;
        }

        if ( !isset( $_POST['mepr_payment_method'] ) && !isset( $_POST['action'] ) ) {
            return $translation;
        }

        if ( !isset( $_POST['mepr_product_id'] ) ) {
            global $post;

            if ( $post == null || !isset( $post->ID ) ) {
                return $translation;
            }

            $post_id = $post->ID;
        }else {
            $post_id = sanitize_text_field( $_POST['mepr_product_id'] );
        }
    
        $checkout_txt = get_post_meta( $post_id, 'bloopanimation-mepr-checkout-txt', true );

        ob_start();
        ?>
        <span class="bloopanimation-logo-n-text">
            <img src="<?php echo esc_url( plugins_url( 'assets/images/bloopAnimation_logo.png', dirname(__DIR__) ) ); ?>" alt="logo">
            <?php if ( !empty( $checkout_txt ) ): ?>
                <span>
                    <?php echo esc_html( $checkout_txt ); ?>
                </span>
            <?php endif ?>
        </span>
        <?php
        return ob_get_clean();
    }

    /**
     * Change translation, "without context"
     * 
     */
    function change_coupon_code_text( $translation, $text, $domain ) {
        $context = '';
        return $this->modify_translation( $translation, $text, $context, $domain );
    }

    /**
     * Change translation, "with context"
     * 
     */
    function change_coupon_code_text_with_context( $translation, $text, $context, $domain ) {
        return $this->modify_translation( $translation, $text, $context, $domain );
    }

    /**
     * Common function to modiy translation
     * 
     */ 
    function modify_translation( $translation, $text, $context, $domain ) {

        if ( !isset( $_POST['mepr_payment_method'] ) && !isset( $_POST['action'] ) ) {
            return $translation;
        }

        if ( !isset( $_POST['mepr_product_id'] ) ) {
            global $post;

            if ( $post == null || !isset( $post->ID ) ) {
                return $translation;
            }

            $post_id = $post->ID;
        }else {
            $post_id = sanitize_text_field( $_POST['mepr_product_id'] );
        }

        $post_title = get_the_title( $post_id );

        if ( $post_title == 'All-Access Pass' ) {

            if ( $translation == "Using Coupon &ndash; %s" ) {
                $translation = 'Upgrade Discount';
            }elseif( $translation == "Coupon Code '%s'" ){
                $translation = 'Upgrade Discount';
            }elseif( $translation == 'Have a coupon?' || $translation == 'Coupon Code:' ) {
                $translation = 'Discount Code';
            }

        }elseif( $translation == 'Have a coupon?' || $translation == 'Coupon Code:' ) {
            $translation = 'Discount Code';
        }

        return $translation;
    }

    /**
     * Show the membership's featured image instead of that grey icon
     * 
     */ 
    function replace_with_featured_img_url( $good_protocol_url, $original_url, $_context ) {

        $substring = '/wp-content/plugins/memberpress/images/checkout/product.png';
        if ( strpos( $good_protocol_url, $substring ) == false ) {
            return $good_protocol_url;
        }

        if ( !isset( $_POST['mepr_product_id'] ) ) {
            global $post;

            if ( $post == null || !isset( $post->ID ) ) {
                return $translation;
            }

            $post_id = $post->ID;
        }else {
            $post_id = sanitize_text_field( $_POST['mepr_product_id'] );
        }
        
        $post_title         = get_the_title( $post_id );
        $featured_image_url = get_the_post_thumbnail_url( $post_id, 'thumbnail' );

        if ( !empty( $featured_image_url ) ) {
            $good_protocol_url = $featured_image_url;
        }
        
        return $good_protocol_url;
    }

    /**
     * Hooking into the 'mepr_coupon_get_discount_amount' filter to customize
     * the discount amount for MemberPress coupons based on user's previous purchases.
     */
    function set_memberpress_coupon_amount( $discount_amount, $obj, $prd ) {

        if ( !is_user_logged_in() ) {
            return $discount_amount;
        }

        $user_id         = get_current_user_id();
        $discount_amount = bloopanimation_get_previous_purchases_value( $user_id );

        return $discount_amount;
    }

    /**
     * Filter callback to modify the 'mepr-invoice' data.
     *
     * This function is hooked to the 'mepr-invoice' filter and is used to remove the
     * " – Payment" suffix from the 'description' field of each item in the 'items' array.
     *
     * @param array $invoice The original invoice data.
     * @param object $txn The transaction object.
     * @return array Modified invoice data with 'description' field in 'items' array updated.
     */
    function remove_payment_txt_from_item( $invoice, $txn ) {
        
        if ( !isset( $_POST['mepr_payment_method'] ) && !isset( $_POST['action'] ) ) {
            return $invoice;
        }

        // Check if 'items' array exists in the $invoice
        if (isset($invoice['items']) && is_array($invoice['items'])) {
            // Loop through each item and modify the 'description' field
            foreach ($invoice['items'] as &$item) {
                if (isset($item['description'])) {
                    $item['description'] = str_replace('&nbsp;&ndash;&nbsp;Payment', '', $item['description']);
                }
            }
        }
        return $invoice;
    }

    /**
     * Enqueue custom CSS styles in the <head> section of the WordPress website.
     *
     * This function is hooked to the 'wp_head' action to add custom CSS styles
     *
     * @return void
     */
    function header_css_styles_logged_in() {

        if ( !is_user_logged_in() ) {
            return;
        }

        global $post;

        if ( $post == null || !isset( $post->ID ) ) {
            return;
        }

        $post_id    = $post->ID;
        $post_title = get_the_title( $post_id );

        ob_start();

        ?>
        <style type="text/css">
            <?php if ( $post_title == 'All-Access Pass' && !empty( bloopanimation_get_previous_purchases_value( $user_id ) ) ): ?>
                .mepr-checkout-container .have-coupon-link {
                    display: none;
                }
            <?php endif ?>
        </style>
        <?php

        echo trim( ob_get_clean() );
    }


    /**
     * Set a default MemberPress coupon on initialization if certain conditions are met.
     */
    function set_memberpress_coupon() {

        if ( !is_user_logged_in() ) {
            return;
        }

        if ( is_admin() ) {
            return;
        }

        if ( isset( $_GET['coupon'] ) ) {
            return;
        }

        global $post;

        if ( $post == null || !isset( $post->ID ) ) {
            return;
        }

        $post_id    = $post->ID;
        $post_title = get_the_title( $post_id );

        if ( $post_title != 'All-Access Pass' ) {
            return;
        }

        $user_id         = get_current_user_id();
        $discount_amount = bloopanimation_get_previous_purchases_value( $user_id );

        if ( empty( $discount_amount ) || $discount_amount == 0 ) {
            return;
        }

        $_GET['coupon'] = 'Upgrade-Discount';
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Checkout_Object = new BACU_BloopAnimation_Customizations_Memberpress_Checkout();
