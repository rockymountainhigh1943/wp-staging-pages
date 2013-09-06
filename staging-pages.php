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
		'name' => 'Staging Pages',
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

function jl_remove_staging_post_page_add_new(){
	global $submenu;
	unset( $submenu['edit.php?post_type=staging-post'][10] );
	unset( $submenu['edit.php?post_type=staging-page'][10] );
}

add_action( 'admin_menu', 'jl_remove_staging_post_page_add_new' );





/*
** Adds our context menu item to the row actions list
*/

function jl_staging_pages_add_row_action( $actions, $page_object ){
	$jl_the_post_type = get_post_type();
	if ( "post" == $jl_the_post_type || "page" == $jl_the_post_type ){

		//var_dump($page_object);

		$jlCheckForStagedArgs = array(
			'meta_key' => 'jl-staged-'.$jl_the_post_type.'-original',
			'meta_value' => $page_object->ID,
			'post_type' => 'staging-' . $jl_the_post_type,
			'posts_per_page' => 1
		);

		$jlCheckForStagedQuery = new WP_Query( $jlCheckForStagedArgs );

		if ( $jlCheckForStagedQuery->have_posts() ){
			while ( $jlCheckForStagedQuery->have_posts() ){
				$jlCheckForStagedQuery->the_post();
	    		$actions['staging_object'] = __('Status').': <a href="'.get_admin_url().'post.php?post='.get_the_ID().'&action=edit" class="jl-not-staged">'.__('Staged').'</a>';
		    }
		} else {
			$myNonce = wp_create_nonce('wp-staging-nonce');
	    	$actions['staging_object'] = __('Status').': <a href="'.get_admin_url().'options.php?jl-mirror-post-id='.$page_object->ID.'&jl-mirror-post-type='.$page_object->post_type.'&_wpnonce='.$myNonce.'" class="jl-not-staged">'.__('Not Staged').'</a>';
		}

	}
	return $actions;
}

add_filter( 'page_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );
add_filter( 'post_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );





/*
** Check if post has a staged post type already registered, if so we create our staged item
*/

function jl_staging_pages_check_for_mirror(){
	if( ! empty( $_GET['jl-mirror-post-id'] ) && ! empty( $_GET['jl-mirror-post-type'] ) ){
		$jlDieMessage = '<h1>You do not have permission to do this. We have your geolocation - you have 1 minute. Good luck.</h1>';
		$myNonce = $_REQUEST['_wpnonce'];

		if ( ! wp_verify_nonce( $myNonce, 'wp-staging-nonce' ) ){
			wp_die( $jlDieMessage ); 
		} else {

			if ( ('post' == $_GET['jl-mirror-post-type']) || ('page' == $_GET['jl-mirror-post-type']) ){
				$jl_mirror_post_type = $_GET['jl-mirror-post-type'];
			} else {
				wp_die( $jlDieMessage );
			}

			if ( is_numeric( $_GET['jl-mirror-post-id'] ) ){
				$jl_mirror_post_id = $_GET['jl-mirror-post-id'];

				$jlCheckForStagedArgs = array(
					'meta_key' => 'jl-staged-post-original',
					'meta_value' => $jl_mirror_post_id,
					'post_type' => 'staging-' . $jl_mirror_post_type,
					'posts_per_page' => 1
				);

				$jlCheckForStagedQuery = new WP_Query( $jlCheckForStagedArgs );

				if ( $jlCheckForStagedQuery->have_posts() ){
					return;
				}

			} else {
				wp_die( $jlDieMessage ); 
			}
			
			// Check to see if this mirrored post type exists
			if ( post_type_exists( 'staging-'.$jl_mirror_post_type ) ){

				$jlNewPostArgs = array(
					'page_id' => $jl_mirror_post_id,
					'post_type' => $jl_mirror_post_type,
					'posts_per_page' => 1
				);

				$jlNewPostQuery = new WP_Query( $jlNewPostArgs );

				if ( $jlNewPostQuery->have_posts() ){
					while( $jlNewPostQuery->have_posts() ){
						$jlNewPostQuery->the_post();

						$stagedTitle = $jlNewPostQuery->posts[0]->post_title;
						$stagedContent =  $jlNewPostQuery->posts[0]->post_content;

						//var_dump($jlNewPostQuery);

						if ( ! empty( $stagedTitle ) && ! empty( $stagedContent ) ){

							$stagedNewItem = array(
								'post_title'    => $stagedTitle,
								'post_content'  => $stagedContent,
								'post_status'   => 'publish',
								'post_type'   => 'staging-'.$jl_mirror_post_type
							);

							$createStagedItem = wp_insert_post( $stagedNewItem );

							if ( is_numeric($createStagedItem) ){
								update_post_meta( $createStagedItem, 'jl-staged-' . $jl_mirror_post_type . '-original', $jl_mirror_post_id );
								wp_safe_redirect( get_admin_url() . '/post.php?post=' . $createStagedItem . '&action=edit' );
							}


						}

					}
				}

			} else {
				echo '<script>alert("Sorry but there is no staging post type for this item."); window.location = "'.get_admin_url().'";</script>';
			}

		}

	}
}

add_action( 'admin_init', 'jl_staging_pages_check_for_mirror' );





/*
** Add our deploy button to the Publish metabox
*/

function jl_staging_pages_add_deploy_button (){ 
	global $pagenow;
	$jl_the_post_type = get_post_type();
	if ( ( 'post.php' == $pagenow ) && ( 'staging-page' == $jl_the_post_type || 'staging-post' == $jl_the_post_type ) ){
		if ( isset($_GET['post']) && is_numeric($_GET['post']) ){
			$itemID = $_GET['post'];
			$deployNonce = wp_create_nonce('jl-staging-pages-deploy-nonce');
		}
?>

<div class="misc-pub-section curtime">
	<input style="float: right;" type="button" value="Deploy" onclick="window.location='<?php echo get_admin_url(); ?>options.php?deploy-item-id=<?php echo $itemID; ?>&type=<?php echo $jl_the_post_type ?>&_wpnonce=<?php echo $deployNonce; ?>'" accesskey="d" id="deploy" class="button button-primary button-large" name="deploy">
	<strong>DEPLOY</strong><br />
	Will overwrite the original
	<div class="clear"></div>
</div>

<?php } 
	}

add_action( 'post_submitbox_misc_actions', 'jl_staging_pages_add_deploy_button' );





/*
** The user has deployed the staged item, lets process it
*/

function jl_staging_pages_deploy_item (){
	global $pagenow;
	if ( 'options.php' == $pagenow && ! empty( $_GET['deploy-item-id'] ) && ! empty( $_REQUEST['_wpnonce'] ) && ! empty( $_GET['type'] ) ) {
		$jlDieMessage = '<h1>You do not have permission to do this. We have your geolocation - you have 1 minute. Good luck.</h1>';
		$deployID = $_GET['deploy-item-id'];
		$deployGetNonce = $_REQUEST['_wpnonce'];
		$deployType = ( $_GET['type'] == 'staging-post' ? 'post' : 'page' );

		if ( ! wp_verify_nonce( $deployGetNonce, 'jl-staging-pages-deploy-nonce' ) ){
			wp_die( $jlDieMessage );
		} else {
			if ( is_numeric($deployID) ){
				$stagingParentID = get_post_meta( $deployID, 'jl-staged-'.$deployType.'-original', true );

				if ( ! is_numeric( $stagingParentID ) ) {
					wp_die( $jlDieMessage );
				} else {
					$jlUpdatePostArgs = array(
						'page_id' => $deployID,
						'post_type' => $_GET['type'],
						'posts_per_page' => 1
					);

					$jlUpdatePostQuery = new WP_Query( $jlUpdatePostArgs );

					if ( $jlUpdatePostQuery->have_posts() ){
						while( $jlUpdatePostQuery->have_posts() ){
							$jlUpdatePostQuery->the_post();

							$stagedTitle = $jlUpdatePostQuery->posts[0]->post_title;
							$stagedContent =  $jlUpdatePostQuery->posts[0]->post_content;

							if ( ! empty( $stagedTitle ) && ! empty( $stagedContent ) ){

								$stagedDeployItem = array(
									'ID' => $stagingParentID,
									'post_title'    => $stagedTitle,
									'post_content'  => $stagedContent
								);

								$createStagedItem = wp_update_post( $stagedDeployItem );

								if ( is_numeric($createStagedItem) ){

									wp_delete_post( $deployID, true );

									wp_safe_redirect( get_admin_url() . '/post.php?post=' . $stagingParentID . '&action=edit' );
								}


							}

						}
					}

				}

			}
		}
	}
}

add_action( 'admin_init', 'jl_staging_pages_deploy_item' );





/*
** Adds our css styles to the staging-pages admin pages
*/

function jl_staging_pages_add_admin_css (){
	global $pagenow;
	$jl_the_post_type = get_post_type();
	if ( ( 'post.php' == $pagenow || 'edit.php' == $pagenow ) && ( 'staging-page' == $jl_the_post_type || 'staging-post' == $jl_the_post_type ) ){
		wp_register_style(
			'jl-wp-staging-pages-stylesheet',
			plugins_url( 'wp-staging-pages/staging-pages.css' ),
			false,
			'1.0.0'
		);
		wp_enqueue_style( 'jl-wp-staging-pages-stylesheet' );
		wp_enqueue_script(
			'jl-wp-staging-pages-js',
			plugins_url( 'wp-staging-pages/staging-pages.js' ),
			array('jquery'),
			'1.0.0'
		);
	}
}

add_action( 'admin_enqueue_scripts', 'jl_staging_pages_add_admin_css' );

?>