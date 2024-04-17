<?php 
class BACU_BloopAnimation_Customizations_Pages {
    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action( 'body_class', array( $this, 'page_is_in_course_category' ), 10, 1 );
    }

    /**
     * Checks if the current page is in the course category.
     *
     * This function checks if the current WordPress page belongs to the 'course' category.
     *
     * @param array $classes An array of CSS classes for the body tag.
     * @return array The modified array of CSS classes.
     */
    function page_is_in_course_category( $classes ) {

        global $post;

        if ( $post == null || !isset( $post->ID ) ) {
            return $classes;
        }

        $post_id = $post->ID;

        if ( get_post_type( $post_id ) != 'page' ) {
            return $classes;
        }

        // Don't do this on archives
        if( in_array( 'archive', $classes ) || in_array( 'category', $classes )  ) {
            return $classes;
        }

        $current_post = $post_id;

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

        $classes[] = 'bloopanimation-page-is-in-course-category';

        return $classes;
    }
}//End of class

/*      
 * Object
 *
 */
$BACU_BloopAnimation_Customizations_Pages_Object = new BACU_BloopAnimation_Customizations_Pages();
