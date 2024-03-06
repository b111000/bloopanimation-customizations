<?php 
class BACU_BloopAnimation_Customizations_Assets {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'css_styles' ) );
    }

    /**
     * CSS
     * 
     * We can't use 'wp_enqueue_scripts' hook on memberpress checkout hook and so we have to use 'wp_head'
     *
     */
    public static function css_styles() {
        ob_start();
        ?>
            <link rel='stylesheet' id='bacu-main' href='<?php echo esc_url( plugins_url( 'assets/css/main.css', dirname(__DIR__) ) ); ?>' type='text/css' media='all' />
        <?php
        echo trim( ob_get_clean() );
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_Assets_Object = new BACU_BloopAnimation_Customizations_Assets();
