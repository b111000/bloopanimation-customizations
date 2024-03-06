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
            __( 'Checkout Page', 'bloopanimation' ),
            array( $this, 'metabox_html'),
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

        ?>
        <table>
            <tbody>
                <tr>
                    <td style="vertical-align: top;">
                        <?php esc_html_e( 'More Details', 'bloopanimation' ); ?>
                    </td>
                    <td>
                        <textarea name="bloopanimation-mepr-checkout-txt" rows="4" cols="50"><?php echo esc_html( $checkout_txt );?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php 
    }

    /**
     * Save post meta
     *
     */
    function save_post_meta( $post_id, $post, $update ) {
        update_post_meta( $post_id, 'bloopanimation-mepr-checkout-txt', sanitize_text_field( $_POST['bloopanimation-mepr-checkout-txt'] ) );
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Memberpress_Checkout_Object = new BACU_BloopAnimation_Customizations_Memberpress_Meta_Fields();
