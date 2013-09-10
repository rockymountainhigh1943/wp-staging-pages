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

function jl_staging_pages_register_staging_post_types () {

	$post_labels = array(
		'name' => __( 'Staging Posts', 'jl-staging-pages' ),
		'singular_name' => __( 'Staged Post', 'jl-staging-pages' ),
		'add_new' => __( 'Add New', 'jl-staging-pages' ),
		'add_new_item' => __( 'Add New Staged Post', 'jl-staging-pages' ),
		'edit_item' => __( 'Edit Staged Post', 'jl-staging-pages' ),
		'new_item' => __( 'New Staged Post', 'jl-staging-pages' ),
		'all_items' => __( 'All Staged Posts', 'jl-staging-pages' ),
		'view_item' => __( 'View Staged Post', 'jl-staging-pages' ),
		'search_item' => __( 'Search Staged Posts', 'jl-staging-pages' ),
		'not_found' => __( 'No staged posts found.', 'jl-staging-pages' ),
		'not_found_in_trash' => __( 'No staged posts found in Trash.', 'jl-staging-pages' ),
		'parent_item_colon' => NULL,
		'menu_name' => __( 'Staged Posts', 'jl-staging-pages' )
	);

	$post_args = array(
		'labels' => $post_labels,
		'description' => __( 'A staging place for posts', 'jl-staging-pages' ),
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
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
		'name' => __( 'Staging Pages', 'jl-staging-pages' ),
		'singular_name' => __( 'Staged Page', 'jl-staging-pages' ),
		'add_new' => __( 'Add New', 'jl-staging-pages' ),
		'add_new_item' => __( 'Add New Staged Page', 'jl-staging-pages' ),
		'edit_item' => __( 'Edit Staged Page', 'jl-staging-pages' ),
		'new_item' => __( 'New Staged Page', 'jl-staging-pages' ),
		'all_items' => __( 'All Staged Pages', 'jl-staging-pages' ),
		'view_item' => __( 'View Staged Page', 'jl-staging-pages' ),
		'search_item' => __( 'Search Staged Pages', 'jl-staging-pages' ),
		'not_found' => __( 'No staged pages found.', 'jl-staging-pages' ),
		'not_found_in_trash' => __( 'No staged pages found in Trash.', 'jl-staging-pages' ),
		'parent_item_colon' => NULL,
		'menu_name' => __( 'Staged Pages', 'jl-staging-pages' )
	);

	$page_args = array(
		'labels' => $page_labels,
		'description' => __( 'A staging place for pages', 'jl-staging-pages' ),
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
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

function jl_staging_pages_add_row_action ( $actions, $page_object ) {
	$jl_the_post_type = get_post_type();
	if ( "post" == $jl_the_post_type || "page" == $jl_the_post_type ){

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

				$current_user = wp_get_current_user();

				if ( get_post_meta( get_the_ID(), 'jl_staging_pages_allowed_users', true ) ) {
					$jlStagingGetSavedUsers = get_post_meta( get_the_ID(), 'jl_staging_pages_allowed_users', true );
				}

				if ( $jlCheckForStagedQuery->posts[0]->post_author == $current_user->ID || ( isset( $jlStagingGetSavedUsers ) && in_array( $current_user->ID, $jlStagingGetSavedUsers ) ) ){
					$actions['staging_object'] = __('Status', 'jl-staging-pages').': <a href="'.get_admin_url().'post.php?post='.get_the_ID().'&action=edit" class="jl-not-staged">'.__('Staged', 'jl-staging-pages').'</a>';
				} else {
	    			$actions['staging_object'] = __('Status', 'jl-staging-pages').': '.__('Private', 'jl-staging-pages');	
				}

		    }
		} else {
			$myNonce = wp_create_nonce('wp-staging-nonce');
	    	$actions['staging_object'] = __('Status', 'jl-staging-pages').': <a href="'.get_admin_url().'options.php?jl-mirror-post-id='.$page_object->ID.'&jl-mirror-post-type='.$page_object->post_type.'&_wpnonce='.$myNonce.'" class="jl-not-staged">'.__('Not Staged', 'jl-staging-pages').'</a>';	
		}

		wp_reset_query();	
		
	}
	return $actions;
}

add_filter( 'page_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );
add_filter( 'post_row_actions', 'jl_staging_pages_add_row_action', 100, 2 );


	

 
/*
** Adds our context menu item to the row actions list for staging pages
*/

function jl_staging_pages_add_row_action_deploy ( $actions, $page_object ) {
	global $pagenow;
	$jl_the_post_type = get_post_type();

	if ( ( "edit.php" == $pagenow ) && ( "staging-post" == $jl_the_post_type || "staging-page" == $jl_the_post_type ) ){
		$myNonce = wp_create_nonce('jl-staging-pages-deploy-nonce');
		$actions['deploy_object'] = '<a href="'.get_admin_url().'options.php?deploy-item-id='.$page_object->ID.'&type='.$page_object->post_type.'&_wpnonce='.$myNonce.'" class="jl-not-staged">'.__('Deploy', 'jl-staging-pages').'</a>';
	}
	return $actions;
}

add_filter( 'page_row_actions', 'jl_staging_pages_add_row_action_deploy', 100, 2 );
add_filter( 'post_row_actions', 'jl_staging_pages_add_row_action_deploy', 100, 2 );





/*
** Check if post has a staged post type already registered, if so we create our staged item
*/

function jl_staging_pages_check_for_mirror () {
	if( ! empty( $_GET['jl-mirror-post-id'] ) && ! empty( $_GET['jl-mirror-post-type'] ) ){
		$jlDieMessage = __( '<h1>You do not have permission to do this. We have your geolocation - you have 1 minute. Good luck.</h1>' , 'jl-staging-pages');
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

						if ( ! empty( $stagedTitle ) || ! empty( $stagedContent ) ){

							$stagedNewItem = array(
								'post_title'    => $stagedTitle,
								'post_content'  => $stagedContent,
								'post_status'   => 'publish',
								'post_type'   => 'staging-'.$jl_mirror_post_type
							);

							$createStagedItem = wp_insert_post( $stagedNewItem );

							if ( is_numeric($createStagedItem) ){
								update_post_meta( $createStagedItem, 'jl-staged-' . $jl_mirror_post_type . '-original', $jl_mirror_post_id );
								wp_safe_redirect( get_admin_url() . 'post.php?post=' . $createStagedItem . '&action=edit' );
							}


						}

					}
				}

			} else {
				echo '<script>alert(' . __( "Sorry but there is no staging post type for this item.", 'jl-staging-pages') .' ); window.location = "'.get_admin_url().'";</script>';
			}

		}

	}
}

add_action( 'admin_init', 'jl_staging_pages_check_for_mirror' );





/*
** Add our deploy button to the Publish metabox
*/

function jl_staging_pages_add_deploy_button () { 
	global $pagenow;
	$jl_the_post_type = get_post_type();
	if ( ( 'post.php' == $pagenow ) && ( 'staging-page' == $jl_the_post_type || 'staging-post' == $jl_the_post_type ) && ( isset($_GET['message']) ) ){
		if ( isset($_GET['post']) && is_numeric($_GET['post']) ){
			$itemID = $_GET['post'];
			$deployNonce = wp_create_nonce('jl-staging-pages-deploy-nonce');
		}
?>

<div class="misc-pub-section curtime">
	<input style="float: right;" type="button" value="Deploy" onclick="window.location='<?php echo get_admin_url(); ?>options.php?deploy-item-id=<?php echo $itemID; ?>&type=<?php echo $jl_the_post_type ?>&_wpnonce=<?php echo $deployNonce; ?>'" accesskey="d" id="deploy" class="button button-primary button-large" name="deploy">
	<strong><?php _e( 'DEPLOY', 'jl-staging-pages' ); ?></strong><br />
	<?php _e( 'Will overwrite the original', 'jl-staging-pages' ); ?>
	<div class="clear"></div>
</div>

<?php } else {
		return;
	}
}

add_action( 'post_submitbox_misc_actions', 'jl_staging_pages_add_deploy_button' );





/*
** The user has deployed the staged item, lets process it
*/

function jl_staging_pages_deploy_item () {
	global $pagenow;
	if ( ( 'options.php' == $pagenow || 'edit.php' == $pagenow ) && ! empty( $_GET['deploy-item-id'] ) && ! empty( $_REQUEST['_wpnonce'] ) && ! empty( $_GET['type'] ) ) {
		$jlDieMessage = __( '<h1>You do not have permission to do this. We have your geolocation - you have 1 minute. Good luck.</h1>', 'jl-staging-pages' );
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

							if ( ! empty( $stagedTitle ) || ! empty( $stagedContent ) ){

								$stagedDeployItem = array(
									'ID' => $stagingParentID,
									'post_title'    => $stagedTitle,
									'post_content'  => $stagedContent
								);

								$createStagedItem = wp_update_post( $stagedDeployItem );

								if ( is_numeric($createStagedItem) ){

									wp_delete_post( $deployID, true );
									wp_safe_redirect( get_admin_url() . 'post.php?post=' . $stagingParentID . '&action=edit' );
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

function jl_staging_pages_add_admin_css () {
	global $pagenow;
	$jl_the_post_type = get_post_type();
	if ( ! $jl_the_post_type ){
		if ( isset( $_GET['post_type'] ) ){
			$jl_the_post_type = esc_html( $_GET['post_type'] );
		}
	}
	if ( ( 'post.php' == $pagenow || 'edit.php' == $pagenow ) && ( 'staging-page' == $jl_the_post_type || 'staging-post' == $jl_the_post_type ) ){
		wp_register_style(
			'jl-wp-staging-pages-stylesheet',
			plugins_url( 'wp-staging-pages/staging-pages.css' ),
			false,
			'1.0.0'
		);
		wp_enqueue_style( 'jl-wp-staging-pages-stylesheet' );
	}
}

add_action( 'admin_enqueue_scripts', 'jl_staging_pages_add_admin_css' );





/*
** Add a meta_box to each staging item, allowing the user to choose audience
*/

function jl_staging_pages_add_user_meta_box () {

	$editScreens = array( 'staging-page', 'staging-post' );

	foreach ( $editScreens as $es ){

		add_meta_box(
			'jl_staging_pages_user_box',
			__( 'Viewers / Editors', 'jl-staging-pages' ),
			'jl_staging_pages_user_box_render',
			$es,
			'side'
		);

	}

}

add_action( 'add_meta_boxes', 'jl_staging_pages_add_user_meta_box' );

function jl_staging_pages_user_box_render ( $post ) {
	$current_user = wp_get_current_user();
	wp_nonce_field( 'jl_staging_pages_user_box_render', 'jl_staging_pages_user_box_render_nonce' );

	if ( get_post_meta( $post->ID, 'jl_staging_pages_allowed_users', true ) ) {
		$jlStagingGetSavedUsers = get_post_meta( $post->ID, 'jl_staging_pages_allowed_users', true );
		function jl_test_array_for_numeric ($in) {
			return is_numeric($in);
		}

		$isValidNumeric = array_filter( $jlStagingGetSavedUsers, 'jl_test_array_for_numeric' );
	}

	if ( $post->post_author == $current_user->ID ){
		$jlStagingUserQueryArgs = array( 'exclude' => $current_user->ID );
		$jlStagingAuthorName = 'You';
	} else {
		$jlStagingUserQueryArgs = array( 'exclude' => $current_user->ID .', '.$post->post_author );
		$jlStagingAuthorName = get_the_author_meta('display_name', $post->post_author);
	}

	echo '<strong>' . __( 'Owner', 'jl-staging-pages') . ':</strong> '.$jlStagingAuthorName.'<br />';
	
	$jlGetAllUsers = new WP_User_Query( $jlStagingUserQueryArgs );
	$jlAuthors = $jlGetAllUsers->get_results();
	
	foreach ( $jlAuthors as $jlAuthor ){

		if ( isset($jlStagingGetSavedUsers) && $isValidNumeric ){
			// We have saved items
			$jlUserIsInArray = in_array( $jlAuthor->ID, $jlStagingGetSavedUsers );
			if ( $jlUserIsInArray ){
				echo '<input class="jl-staging-checkbox" type="checkbox" id="staging-pages-user-'.$jlAuthor->ID.'" name="staging-pages-users[]" value="'.$jlAuthor->ID.'" checked="checked" /> <label for="staging-pages-user-'.$jlAuthor->ID.'" />'.$jlAuthor->display_name.'</label><br />';
			} else {
				echo '<input class="jl-staging-checkbox" type="checkbox" id="staging-pages-user-'.$jlAuthor->ID.'" name="staging-pages-users[]" value="'.$jlAuthor->ID.'" /> <label for="staging-pages-user-'.$jlAuthor->ID.'" />'.$jlAuthor->display_name.'</label><br />';
			}

		} else {
			// No saved items
			echo '<input class="jl-staging-checkbox" type="checkbox" id="staging-pages-user-'.$jlAuthor->ID.'" name="staging-pages-users[]" value="'.$jlAuthor->ID.'" /> <label for="staging-pages-user-'.$jlAuthor->ID.'" />'.$jlAuthor->display_name.'</label><br />';
		}

	}
}

function jl_staging_pages_save_meta_box_values ( $post_id ){

	if ( isset($_POST['post_type']) && ( ('staging-page' == $_POST['post_type']) || ('staging-post' == $_POST['post_type']) ) ){

		if ( ! isset($_POST['jl_staging_pages_user_box_render_nonce']) ){
			$jlDieNoticeRenderNonce = __( 'Something happened and WordPress cannot find your nonce. Nice try.', 'jl-staging-pages' );
			wp_die($jlDieNoticeRenderNonce);
		} else {
			$jlSaveMetaBoxNonce = $_POST['jl_staging_pages_user_box_render_nonce'];
		}

		if ( ! wp_verify_nonce( $jlSaveMetaBoxNonce, 'jl_staging_pages_user_box_render' ) ){
			$jlDieNoticeRenderNonceNotVerify = __( 'Something happened and WordPress cannot verify your nonce. Nice try.', 'jl-staging-pages' );
			wp_die($jlDieNoticeRenderNonceNotVerify);
		} else {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
				return $post_id;
			} else {

					if ( ! current_user_can( 'edit_page', $post_id ) ){
						$jlDieNoticeCantEdit = __( '<h1>You do not have permission to do this. We have your geolocation - you have 1 minute. Good luck.</h1>', 'jl-staging-pages' );
						wp_die($jlDieNoticeCantEdit);
					} else {

						if ( $_POST['staging-pages-users'] ){
							function jl_test_array_for_numeric ($in) {
								return is_numeric($in);
							}

							$isValidNumeric = array_filter( $_POST['staging-pages-users'], 'jl_test_array_for_numeric' );

							if ( $isValidNumeric ){
								update_post_meta( $post_id, 'jl_staging_pages_allowed_users', $_POST['staging-pages-users'] );
							}
						} else {
							delete_post_meta( $post_id, 'jl_staging_pages_allowed_users' );
						}

					}

			}

		}

	}
}

add_action( 'save_post', 'jl_staging_pages_save_meta_box_values' );





/*
** Lets hide staging items from users that do not have permission to view / edit
*/

function jl_staging_pages_get_items_to_hide () {
	global $pagenow, $post_type;
	global $jlUserAccess;

	if ( ! $post_type ){
		if ( ! empty($_GET['post_type']) ){
			$post_type = esc_html($_GET['post_type']);
		}
	}

	if ( ( "edit.php" == $pagenow ) && ( "staging-page" == $post_type || "staging-post" == $post_type ) ){
		$current_user = wp_get_current_user();
		$jlHideItemsArgs = array(
				'post_type' => $post_type,
                'posts_per_page' => -1
			);
		$jlStagingPagesHideItemsQuery = new WP_Query( $jlHideItemsArgs );
		if ( $jlStagingPagesHideItemsQuery->have_posts() ){
			$jlUserAccess = array();
			while ( $jlStagingPagesHideItemsQuery->have_posts() ){
				$jlStagingPagesHideItemsQuery->the_post();

				$jlStagingPagesAuthorID = get_the_author_meta('ID');

				$jlCheckThePostMeta = get_post_meta( get_the_ID(), 'jl_staging_pages_allowed_users', true );
				
				if ( $jlCheckThePostMeta ){
					// we have post meta
					$jlCheckCurrentUserStatus = in_array( $current_user->ID,  $jlCheckThePostMeta);
					if ( $jlCheckCurrentUserStatus || $current_user->ID == $jlStagingPagesAuthorID ){
						//$jlUserAccess[] = get_the_ID();
						array_push($jlUserAccess, get_the_ID());
					}
				} elseif ( $current_user->ID == $jlStagingPagesAuthorID ) {
					//$jlUserAccess[] = get_the_ID();
					array_push($jlUserAccess, get_the_ID());
				}

			}
		}
		wp_reset_query();
	}

	function jl_staging_pages_hide_items_from_unauthorized ( $query ) {
		global $pagenow, $post_type;

		if ( ( "edit.php" == $pagenow ) && ( "staging-page" == $post_type || "staging-post" == $post_type ) ){
			global $jlUserAccess;
			if ( $jlUserAccess ){
				$query->query_vars['post__in'] = $jlUserAccess;
			} else {
				$query->query_vars['post__in'] = array('0');
			}
		}
	}

	add_action( 'parse_query', 'jl_staging_pages_hide_items_from_unauthorized' );

}

add_action( 'admin_init', 'jl_staging_pages_get_items_to_hide' );





/*
** Add i18n support
*/

function jl_staging_pages_i18n_init() {
 $plugin_dir = basename(dirname(__FILE__));
 load_plugin_textdomain( 'jl-staging-pages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'jl_staging_pages_i18n_init');

?>