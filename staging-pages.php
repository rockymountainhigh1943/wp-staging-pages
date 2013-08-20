<?php
/*
Plugin Name: Staging Pages
Plugin URI: http://jakelovedev.com/plugins/staging-pages
Version: 0.1
Description: Adds the ability for users to setup "staging" versions of posts, pages, and custom posts types. Once approved, the user can easily deploy.
Author: Jake Love
Author URI: http://jakelovedev.com/
Licence: GPL2
*/


/*
** Adds our context menu item to the row actions list
*/

function jl_staging_pages_add_row_action( $actions, $page_object ){
    $actions['staging_object'] = __('Status').': <a href="'.get_admin_url().'options.php?jl-mirror-post-id='.$page_object->ID.'&jl-mirror-post-type='.$page_object->post_type.'" class="jl-not-staged">'.__('Not Staged').'</a>';
    return $actions;
}

add_filter( 'page_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );
add_filter( 'post_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );



/*
** Check if post has a staged post type already registered
*/

function jl_staging_pages_check_for_mirror(){
	if( ! empty( $_GET['jl-mirror-post-id'] ) && ! empty( $_GET['jl-mirror-post-type'] ) ){
		if ( is_numeric( $_GET['jl-mirror-post-id'] ) ){
			$jl_mirror_post_id = $_GET['jl-mirror-post-id'];
		}
		$jl_mirror_post_type = esc_html( $_GET['jl-mirror-post-type'] );
		var_dump($jl_mirror_post_id);
		var_dump($jl_mirror_post_type);
	}
}

add_action( 'admin_init', 'jl_staging_pages_check_for_mirror' );

?>