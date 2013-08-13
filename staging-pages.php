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

function jl_staging_pages_add_row_action( $actions, $page_object ){
  $actions['staging_object'] = 'Status: <a href="#not-staged" class="jl-not-staged">'.__('Not Staged').'</a>';
  return $actions;
}

add_filter( 'page_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );
add_filter( 'post_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );

?>