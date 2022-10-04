<?php
/**
 * Helper functions
 *
 * @since      2.0.0
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/includes
 * @author     Dinkum Interactive
 */

/**
 * Check nodes start.
 *
 * @param string $node_type
 * @param array $nodes
 * @return void
 */
function pmh_nodes_import_check_start( $node_type, $nodes ) {

	$node_ids = get_option( "fgd2wp_last_node_${node_type}_queued_ids", array() );

	if ( $nodes ) {

		foreach( $nodes as $node ) {

			if ( ! in_array( $node['nid'], $node_ids ) ) {

				$node_ids[] = $node['nid'];
			}
		}
	}

	update_option( "fgd2wp_last_node_${node_type}_queued_ids", $node_ids );
}

/**
 * Remove completed Node ID.
 *
 * @param string $node_type
 * @param array $nodes
 * @return void
 */
function pmh_nodes_import_check_stops( $node_type, $node_id ) {

	$node_ids = get_option( "fgd2wp_last_node_${node_type}_queued_ids" );

	if ( $node_ids ) {

		$position = array_search( $node_id, $node_ids );

		if ( $position !== false ) {

			unset( $node_ids[ $position ] );

			$node_ids = array_values( $node_ids );

			update_option( "fgd2wp_last_node_${node_type}_queued_ids", $node_ids );
		}
	}
}
