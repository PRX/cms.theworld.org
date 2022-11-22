<?php
/**
 * Plugin Name: FG DI Migrate
 * Plugin URI:
 * Description:
 * Author: Dinkum
 * Version: 0.0.1
 * Author URI:
 *
 * Extend FG Drupal to WP Migration
 *
 * @package FG DI Migrate
 * @version 0.0.1
 */

function fg_di_migrateget_nodes() {
	global $wpdb;

	$sql     = 'SELECT * FROM drupal_authors';
	$results = $wpdb->get_results( $sql );
	return $results;
}

function fg_di_migrateget_team_id( $author ) {
	global $wpdb;
	$cache_key = sprintf( 'pmh_post_by_meta__fgd2wp_old_node_id_%s', sanitize_key( str_replace( '-', '_', $author ) ) );
	$post_id = wp_cache_get( $cache_key );
	if ( ! $post_id ) {
		$sql  = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_fgd2wp_old_node_id' AND meta_value = {$author}";
		$post_id = $wpdb->get_var( $sql );

		if ( $post_id ) {
			wp_cache_set( $cache_key, $post_id );
		}
	}
	$team = null;
	if ( is_numeric( $post_id ) ) {
		$team = get_post( $post_id );
	}

	return ( $team && ! is_wp_error( $team ) && 'team' === $team->post_type ) ? $team->ID : null;
}

function fg_di_migrateget_post( $nid ) {
	global $wpdb;
	$cache_key = sprintf( 'pmh_post_by_meta__fgd2wp_old_node_id_%s', sanitize_key( str_replace( '-', '_', $nid ) ) );
	$post_id = wp_cache_get( $cache_key );
	if ( ! $post_id ) {
		$sql     = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_fgd2wp_old_node_id' AND meta_value = {$nid}";
		$post_id = $wpdb->get_var( $sql );

		if ( $post_id ) {
			wp_cache_set( $cache_key, $post_id );
		}
	}
	$dp_post = null;
	if ( is_numeric( $post_id ) ) {
		$dp_post = get_post( $post_id );
	}
	return ( $dp_post && ! is_wp_error( $dp_post ) && 'post' === $dp_post->post_type ) ? $dp_post : null;
}

function fg_di_migratemigrate_authors() {
	// Get stored nodes.
	$nodes         = fg_di_migrateget_nodes();
	$updated_posts = array();

	if ( $nodes ) {
		$node_teams = array();

		foreach ( $nodes as $node ) {
			// Get WP team ID.
			$team_id = fg_di_migrateget_team_id( $node->field_author_target_id );
			if ( $team_id ) {
				// IF the team id exists associate it to the node.
				$node_teams[ $node->nid ][] = strval( $team_id );
			}
		}
		if ( $node_teams ) {
			foreach ( $node_teams as $nid => $teams ) {
				$dp_post = fg_di_migrateget_post( $nid );
				// For each node check if it exists and if it is a post.
				if ( $dp_post ) {
					// if exists and is valid post, update related authors.
					update_post_meta( $dp_post->ID, 'related_author', $teams );
					$updated_posts[ $dp_post->ID ] = $dp_post->post_title;
				}
			}
		}

		if ( $updated_posts ) {
			echo '<h3>Migrated Authors</h3>';
			foreach ( $updated_posts as $id => $title ) {
				echo "<strong>{$id}</strong> - {$title} <br>";
			}
		}
	}
}

function fg_di_migratemigrate_plugin_setup_menu() {
	add_menu_page( 'FG DI Migrate', 'FG DI Migrate', 'manage_options', 'fg-di-migrate', 'fg_di_migratemigrate' );
}
add_action( 'admin_menu', 'fg_di_migratemigrate_plugin_setup_menu' );

function fg_di_migratemigrate() {
	?>
	<h2>Click to relate authors to posts</h2>
	<form method="POST">
		<button type="submit" class="button button-primary" name="relate_authors">Relate Authors to Publications and Pulse Posts</button>
	</form>
	<?php
	if ( isset( $_REQUEST['relate_authors'] ) ) {
		fg_di_migratemigrate_authors();
	}
}
