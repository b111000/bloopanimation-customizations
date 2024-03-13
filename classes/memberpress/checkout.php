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
        add_filter( 'the_content', array( $this, 'payment_options' ), 900, 1 );

        add_action( 'wp_print_styles', array( $this, 'header_css_styles' ) );
        add_action( 'template_redirect', array( $this, 'set_memberpress_coupon' ) );
        add_action( 'mepr-checkout-after-email-field', array( $this, 'login_link' ) );
        add_action( 'template_redirect', array( $this, 'set_in_cart_product_id' ) );
        add_action( 'cs_masthead', array( $this, 'add_to_cart_button' ) );

        add_action( 'wp_ajax_nopriv_bloopanimation_login', array( $this, 'login' ) );
        add_action( 'wp_ajax_bloopanimation_login', array( $this, 'login' ) );
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
            <a href="<?php echo esc_url( home_url('/') ); ?>">
                <img src="<?php echo esc_url( plugins_url( 'assets/images/bloopAnimation_logo.png', dirname(__DIR__) ) ); ?>" alt="logo">
            </a>
            <?php if ( !empty( $checkout_txt ) ): ?>
                <span>
                    <?php echo wp_kses_post( $checkout_txt ); ?>
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
                $translation = apply_filters( 'bloopanimation-mepr-coupon-code-text', $translation );
            }

        }elseif( $translation == 'Have a coupon?' ) {
            $translation = 'Discount Code';
            $translation = apply_filters( 'bloopanimation-mepr-coupon-code-text', $translation );
        }elseif( $translation == 'Coupon Code:' ) {
            $translation = 'Discount Code';
            $translation = apply_filters( 'bloopanimation-mepr-coupon-code-text-after-click', $translation );
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

        if ( !isset( $_POST['mepr_payment_method'] ) && !isset( $_POST['action'] ) ) {
            return $discount_amount;
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

        $product         = new MeprProduct( $post_id );
        $price           = $product->price;
        $user_id         = get_current_user_id();
        $discount_amount = bloopanimation_get_previous_purchases_value( $user_id );

        if ( $discount_amount >= $price ) {
            $discount_amount = $price;
        }

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
    function header_css_styles() {

        global $post;

        if ( $post == null || !isset( $post->ID ) ) {
            return;
        }

        $post_id    = $post->ID;
        $post_title = get_the_title( $post_id );

        ob_start();

        ?>
        <style type="text/css">
            <?php if ( $post_title == 'All-Access Pass' ): ?>
                .mepr-checkout-container .have-coupon-link {
                    display: none;
                }
            <?php endif ?>
            <?php if ( isset( $_SESSION['bloopanimation-in-cart-product-id'] ) ): ?>
                .x-masthead .x-bar-container.e115109-e10 {
                    margin-right: 40px;
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

    /**
     * Add login link
     * 
     */ 
    function login_link( $product_id ) {
        if ( is_user_logged_in() ) {
            return;
        }
        ?>
        <p class="bloopanimation-login-link-wrapper">
            <?php esc_html_e( 'Already have an account?', 'bloopanimation' ); ?>
            <a href="<?php echo esc_url( wp_login_url() ); ?>" id="bloopanimation-login-link">
                <?php esc_html_e( 'Sign in', 'bloopanimation' ); ?>
            </a>
        </p>
        <div class="bloopanimation-login-section">

            <p id="bloopanimation-validation-msg">
            </p>

            <div class="mp-form-row">
                <div class="mp-form-label">
                    <label><?php esc_html_e( 'Username', 'bloopanimation' ); ?>:*</label>
                </div>
                <input type="text" id="bloopanimation-login-username">
            </div>

            <div class="mp-form-row">
                <div class="mp-form-label">
                    <label><?php esc_html_e( 'Password', 'bloopanimation' ); ?>:*</label>
                </div>
                <input type="password" id="bloopanimation-login-password">
            </div>

            <button>
                <?php esc_html_e( 'Sign in', 'bloopanimation' ); ?>
                <div class="bloopanimation-spinner"></div> 
            </button>

        </div>
        <?php
    }

    /**
     * Set in cart product id to be used to generate the "add to cart" link
     * 
     */ 
    function set_in_cart_product_id() {

        if ( is_admin() ) {
            return;
        }

        global $post;

        if ( $post == null || !isset( $post->ID ) ) {
            return;
        }

        $post_id = $post->ID;

        if ( get_post_type() != 'memberpressproduct' ) {
            return;
        }

        // Start the session
        if ( session_status() == PHP_SESSION_NONE ) {
            session_start();
        }

        // Set product id
        $_SESSION['bloopanimation-in-cart-product-id'] = $post_id;
    }

    /**
     * Show add to cart link or button
     * 
     */ 
    function add_to_cart_button() {

        if ( !isset( $_SESSION['bloopanimation-in-cart-product-id'] ) ) {
            return;
        }

        $product_id = $_SESSION['bloopanimation-in-cart-product-id'];

        ?>
        <div class="bloopanimation-header-cart">
            <a href="<?php echo get_the_permalink( $product_id ); ?>">
                <i class="wpmenucart-icon-shopping-cart-0" ></i>
                <div class="bloopanimation-bubble">1</div>
            </a>
        </div>
        <?php
    }

    /**
     * Payment options on checkout page html
     * 
     */ 
    function payment_options( $content ) {

        if ( !isset( $_POST['mepr_payment_method'] ) && !isset( $_POST['action'] ) ) {
            return $content;
        }

        if ( isset( $_GET['coupon'] ) && $_GET['coupon'] == 'Upgrade-Discount' ) {
            return $content;
        }

        global $post;
        $post_id  = -1;
        if ( $post != null && isset( $post->ID ) ) {
            $post_id = $post->ID;
        }

        if ( get_the_title( $post_id ) != 'All-Access Pass' && get_the_title( $post_id ) != 'All-Access Pass (Payments)' ) {
            return $content;
        }

        ob_start();
        ?>
        <div class="bloopanimation-payment-options-wrapper">

            <?php

                $products = [];
                $args = array(
                    's'              => 'All-Access Pass',
                    'orderby'        => 'relevance',
                    'fields'         => 'ids',
                    'post_status'    => 'publish',
                    'post_type'      => array( 'memberpressproduct' ),
                    'posts_per_page' => 1,
                    'post__not_in'   => array( $post_id ),
                ); 

                $products = get_posts( $args );
                array_unshift( $products, $post_id );
            ?>

            <?php if ( count( $products ) >= 1 ): ?>
                <?php foreach ( $products as $product_id ): ?>

                    <?php
                        $product          = new MeprProduct( $product_id );
                        $price            = $product->price;
                        $limit_cycles_num = $product->limit_cycles_num;
                        $limit_cycles     = $product->limit_cycles;
                    ?>

                    <a href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>" class="bloopanimation-payment-option">
                        <div>
                            <strong>
                                <?php echo get_the_title( $product_id ); ?>
                            </strong>
                            <?php if ( !empty( $limit_cycles_num ) && !empty( $limit_cycles ) ): ?>
                                <p>
                                    <?php echo $limit_cycles_num . ' monthly payments of $' .$price;?>
                                </p>
                            <?php endif ?>
                        </div>
                        <div>
                            $<?php echo esc_html( $price ); ?> USD
                        </div>
                    </a>
                <?php endforeach ?>
            <?php endif ?>

        </div>

        <?php 
        $payments_html = ob_get_clean();

        // Use regex to find the position after the closing tag of the "have-coupon-link" element
        $pattern = '/<a\s+class=["\']have-coupon-link["\'][^>]*>.*?<\/a>/is';

        // Perform the replacement
        $content = preg_replace_callback( $pattern, function ( $matches ) use ( $payments_html ) {
            return $matches[0] . $payments_html;
        }, $content);

        return $content;
    }

    /**
     * Login
     * 
     */ 
    function login() {
        // Check AJAX request
        check_ajax_referer( 'bloopanimation_nonce', 'ajax_nonce' );

        $data                   = [];
        $username               = sanitize_text_field( $_POST['username'] );
        $password               = sanitize_text_field( $_POST['password'] );

        $creds['user_login']    = $username;
        $creds['user_password'] = $password;
        $creds['remember']      = true; 
        $user                   = wp_signon( $creds, false );

        // Login unsuccessful
        if ( is_wp_error( $user ) ) {
            wp_send_json_error( __( 'Invalid username or password.', 'bloopanimation' ) );
        }

        // Login successful
        $data['success'] = true;
        wp_send_json( $data );
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Checkout_Object = new BACU_BloopAnimation_Customizations_Memberpress_Checkout();
