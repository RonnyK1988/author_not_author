<?php
// Authoren können nur ihre und Admin Posts sehen

function restrict_author_posts( $query ) {

    if ( is_user_logged_in() && current_user_can( 'author' ) ) {

        $current_user_id = get_current_user_id();
        

        $author_ids = array( $current_user_id );
        
        if ( !current_user_can( 'administrator' ) ) {

            $admin_ids = get_users( array( 'role__in' => array( 'administrator' ), 'fields' => 'ID' ) );
            $author_ids = array_merge( $author_ids, $admin_ids );
        }
        
        $query->set( 'author__in', $author_ids );
    }
}
add_action( 'pre_get_posts', 'restrict_author_posts' );


// Authoren werden auf die Startseite geleitet wenn sie Beiträge von anderen Authoren öffnen wollen
function restrict_post_access() {
    if ( is_singular( 'post' ) && current_user_can( 'author' ) ) {
        $post = get_queried_object();
        
        if ( $post && $post->post_author != get_current_user_id() ) {

            wp_redirect( home_url() );
            exit;
        }
    }
}
add_action( 'template_redirect', 'restrict_post_access' );

// BuddyPress activity feed wird gefiltert

function filter_buddypress_activity_feed( $args ) {
    if ( is_user_logged_in() && current_user_can( 'author' ) ) {
        $current_user_id = get_current_user_id();
        $args['object'] = 'post';
        $args['primary_id'] = $current_user_id;
    }
    return $args;
}
add_filter( 'bp_after_has_activities_parse_args', 'filter_buddypress_activity_feed' );

?>
