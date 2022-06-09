<?php

$node_types = array(
	'episode',
	'page',
	'person',
	'program',
	'story',
);

 ?>
<tr>
	<th scope="row"><?php _e('Import Check:', 'fgd2wpp'); ?></th>
	<td>
		<div id="pmh-last-node-id">
			<strong>Last Node IDs</strong>
			<br>
			<ul style="margin-top: 0px; padding-left: 20px;">
				<?php foreach( $node_types as $node_type ) : ?>
					<?php $node_id = get_option( "fgd2wp_last_node_${node_type}_id" ); ?>
					<?php $node_id = $node_id ? $node_id : '-'; ?>
					<li><?php echo wp_sprintf( '%s: %s', $node_type, $node_id ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div id="pmh-last-node-queued-ids">
			<strong>Queued Failed Node IDs</strong>
			<br>
			<ul style="margin-top: 0px; padding-left: 20px;">
				<?php foreach ( $node_types as $node_type ) : ?>
					<?php $queued_nodes = get_option( "fgd2wp_last_node_${node_type}_queued_ids" ); ?>
					<?php $queued_nodes = $queued_nodes ? implode( ', ', $queued_nodes ) : 'none'; ?>
					<li><?php echo wp_sprintf( '%s: %s', $node_type, $queued_nodes ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</td>
</tr>
