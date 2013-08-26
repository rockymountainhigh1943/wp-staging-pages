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
** Register staging post types for Posts and Pages
*/

function jl_staging_pages_register_staging_post_types(){

	$post_labels = array(
		'name' => 'Staging Posts',
		'singular_name' => 'Staged Post',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Staged Post',
		'edit_item' => 'Edit Staged Post',
		'new_item' => 'New Staged Post',
		'all_items' => 'All Staged Posts',
		'view_item' => 'View Staged Post',
		'search_item' => 'Search Staged Posts',
		'not_found' => 'No staged posts found.',
		'not_found_in_trash' => 'No staged posts found in Trash.',
		'parent_item_colon' => NULL,
		'menu_name' => 'Staged Posts'
	);

	$post_args = array(
		'labels' => $post_labels,
		'description' => 'A staging place for posts',
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => true,
		'show_in_admin_bar' => false,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'staging-post'),
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 5,
		'supports' => array( 'title', 'editor', 'custom-fields' )
	);

	register_post_type( 'staging-post', $post_args );

	$page_labels = array(
		'name' => 'Staging Pagess',
		'singular_name' => 'Staged Page',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Staged Page',
		'edit_item' => 'Edit Staged Page',
		'new_item' => 'New Staged Page',
		'all_items' => 'All Staged Pages',
		'view_item' => 'View Staged Page',
		'search_item' => 'Search Staged Pages',
		'not_found' => 'No staged pages found.',
		'not_found_in_trash' => 'No staged pages found in Trash.',
		'parent_item_colon' => NULL,
		'menu_name' => 'Staged Pages'
	);

	$page_args = array(
		'labels' => $page_labels,
		'description' => 'A staging place for pages',
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => true,
		'show_in_admin_bar' => false,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'staging-page'),
		'capability_type' => 'page',
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 20,
		'supports' => array( 'title', 'editor', 'custom-fields', 'page-attributes' )
	);

	register_post_type( 'staging-page', $page_args );

}

add_action( 'init', 'jl_staging_pages_register_staging_post_types' );

/*
** Hides the "Add New" from sidebar menu
*/

function jl_remove_staging_post_add_new(){
	global $submenu;
	unset( $submenu['edit.php?post_type=staging-post'][10] );
}

add_action( 'admin_menu', 'jl_remove_staging_post_add_new' );

/*
** Hides the "Add New" from edit screen
*/

function jl_remove_staging_post_add_new_edit_screen (){
	global $pagenow;
	if ( isset( $_GET['post_type'] ) && 'edit.php' == $pagenow && 'staging-post' == $_GET['post_type'] ){
		echo '<style>.add-new-h2 { display: none; }</style>';
	}
}

add_action( 'admin_head', 'jl_remove_staging_post_add_new_edit_screen' );


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
		
		// Check to see if this mirrored post type exists
		if ( post_type_exists( 'staged-'.$jl_mirror_post_type ) ){
			// The staging post type has already been created, proceed with staging content
		} else {
			echo '<script>alert("Sorry but there is no staging post type for this item."); window.location = "'.get_admin_url().'"; </script>';
		}
	}
}

add_action( 'admin_init', 'jl_staging_pages_check_for_mirror' );

?>