<?php 
class BACU_BloopAnimation_Customizations_Memberpress_Meta_Fields {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) ); 
        add_action( 'save_post_memberpressproduct', array( $this, 'save_post_meta' ), 10, 3 );
    }

    /**
     * Add metabox to CPT
     *
     */
    function add_metabox() {
        add_meta_box(
            'bloopanimation-memberpressproduct-checkout-page',
            __( 'Checkout Details', 'bloopanimation' ),
            array( $this, 'metabox_html'),
            ['memberpressproduct'],
            'normal',
            'high',
        );
        add_meta_box(
            'bloopanimation-checkout-page-related-products',
            __( 'Related Products', 'bloopanimation' ),
            array( $this, 'related_products'),
            ['memberpressproduct'],
            'normal',
            'high',
        );
    }

    /**
     * Metabox HTML
     *
     */
    function metabox_html() {

        $post_id      = get_the_ID();
        $checkout_txt = get_post_meta( $post_id, 'bloopanimation-mepr-checkout-txt', true );

        wp_editor( $checkout_txt, 'bloopanimation-mepr-checkout-txt', array(
            'media_buttons' => false,
            'textarea_name' => 'bloopanimation-mepr-checkout-txt',
            'textarea_rows' => 10,
        ) );

        ?>
        <p>
           <?php esc_html_e( 'Here\'s an example on how to customize the output with CSS. Paste the code e.g. in a Code Snippets plugin.', 'bloopanimation' ); ?> 
        </p>
        <p>
            <code>
                .bloopanimation-logo-n-text {
                    color: red;
                }
            </code>
        </p>
        <?php 
    }

    /**
     * Metabox HTML
     *
     */
    function related_products() {

        $post_id          = get_the_ID();
        $related_products = get_post_meta( $post_id, 'bloopanimation-mepr-related-products', true );

        ?>
        <textarea cols="27" rows="7" placeholder="Please enter 1 product ID per line" name="bloopanimation-mepr-related-products"><?php echo esc_html( $related_products ); ?></textarea>
        <?php 
        
    }

    /**
     * Save post meta
     *
     */
    function save_post_meta( $post_id, $post, $update ) {
        update_post_meta( 
            $post_id, 
            'bloopanimation-mepr-checkout-txt', 
            wp_kses_post( $_POST['bloopanimation-mepr-checkout-txt'] ) 
        );
        update_post_meta(
            $post_id, 
            'bloopanimation-mepr-related-products', 
            wp_kses_post( $_POST['bloopanimation-mepr-related-products'] )
        );
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Checkout_Object = new BACU_BloopAnimation_Customizations_Memberpress_Meta_Fields();
