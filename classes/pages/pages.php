<?php 
class BACU_BloopAnimation_Customizations_Pages {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action( 'body_class', array( $this, 'hide_my_course_element' ), 10, 1 );
    }

    /**
     * Hide my course element
     */ 
    function hide_my_course_element( $classes ) {

        if ( get_post_type() != 'page' ) {
            return $classes;
        }

        $current_post = get_the_ID();

        global $wpdb;
        $post_id = $wpdb->get_var(
            $wpdb->prepare(
                "
                    SELECT tr.object_id
                    FROM {$wpdb->term_relationships} tr
                    INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                    WHERE tr.object_id = %d
                    AND tt.taxonomy = %s
                    AND t.slug = %s
                ", $current_post, 'category', 'course')
        );

        if ( empty( $post_id ) ) {
            return $classes;
        }

        $classes[] = 'bloopanimation-hide-my-course-element';

        return $classes;
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_BloopAnimation_Customizations_Pages_Object = new BACU_BloopAnimation_Customizations_Pages();
