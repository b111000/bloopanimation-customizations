<?php 
class BACU_BloopAnimation_Customizations_Assets {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'css_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
    }

    /**
     * CSS
     * We can't use 'wp_enqueue_scripts' hook on memberpress checkout hook and so we have to use 'wp_head'
     *
     */
    public static function css_styles() {
        ob_start();
        ?>
            <link rel='stylesheet' id='bloopanimation-main' href='<?php echo esc_url( plugins_url( 'assets/css/main.css', dirname(__DIR__) ) ); ?>?ver=1.0.0.11xc23-8121' type='text/css' media='all' />
        <?php
        echo trim( ob_get_clean() );
    }

    /**
     * JS/CSS
     * 
     */ 
    function enqueue_scripts_styles() {

        // CSS
        wp_enqueue_style( 'bloopanimation-cornerstone-tabs', plugins_url('assets/css/cornerstone-tabs.css',  dirname(__DIR__) ), array(), '2.3.4', 'all' );

        // JS
        wp_enqueue_script( 'bloopanimation-cornerstone-tabs', plugins_url('assets/js/cornerstone-tabs.js',  dirname(__DIR__) ), array('jquery'), '1.0.012x90', true );
        if ( !is_user_logged_in() ) {
            wp_enqueue_script( 'bloopanimation-groundhogg', plugins_url('assets/js/groundhogg.js',  dirname(__DIR__) ), array('jquery'), '1.0.10901gh67', true );
        }

        wp_enqueue_script( 'bloopanimation-main', plugins_url('assets/js/main.js',  dirname(__DIR__) ), array('jquery'), '1.0.012x90', true );

        $ajax_nonce = wp_create_nonce( 'bloopanimation_nonce' );
        wp_localize_script( 'bloopanimation-main', 'bloopanimation_object', 
            array( 
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'ajax_nonce' => $ajax_nonce,
            )
        );
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Assets_Object = new BACU_BloopAnimation_Customizations_Assets();
