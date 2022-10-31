<?php

$fgd2wp_pmh_chunks_size              = isset( $data['fgd2wp_pmh_chunks_size'] ) ? (int) $data['fgd2wp_pmh_chunks_size'] : 10;
$fgd2wp_pmh_how_many_nodes_to_import = isset( $data['fgd2wp_pmh_how_many_nodes_to_import'] ) ? (int) $data['fgd2wp_pmh_how_many_nodes_to_import'] : 0;
$fgd2wp_skip_blank_titles            = isset( $data['fgd2wp_skip_blank_titles'] ) ? (int) $data['fgd2wp_skip_blank_titles'] : 0;
 ?>
<tr>
	<th scope="row"><?php _e('Import Check:', 'fgd2wpp'); ?></th>
	<td>
		<div id="fgd2wp_pmh_chunks_size-field" style="margin-bottom: 8px;">
			<strong style="display: block;"><?php esc_html_e( 'Chunk Size', 'fgd2wp' ); ?></strong>
			<input
				type="number"
				id="fgd2wp_pmh_chunks_size"
				name="fgd2wp_pmh_chunks_size"
				value="<?php echo esc_attr( $fgd2wp_pmh_chunks_size ); ?>"
				min="0"
			>
		</div>
		<div id="fgd2wp_pmh_how_many_nodes_to_import-field" style="margin-bottom: 8px;">
			<strong style="display: block;"><?php esc_html_e( 'Nodes to Import per node type. This value should be the same or higher than the Chunk Size.', 'fgd2wp' ); ?></strong>
			<input
				type="number"
				id="fgd2wp_pmh_how_many_nodes_to_import"
				name="fgd2wp_pmh_how_many_nodes_to_import"
				value="<?php echo esc_attr( $fgd2wp_pmh_how_many_nodes_to_import ); ?>"
				min="0"
			>
		</div>
		<div id="fgd2wp_skip_blank_titles" style="margin-bottom: 8px;">
			<strong style="display: block;"><?php esc_html_e( 'Skip Blank Titles', 'fgd2wp' ); ?></strong>
			<input
				id="fgd2wp_skip_blank_titles"
				name="fgd2wp_skip_blank_titles"
				type="checkbox"
				value="1" <?php checked( $data['fgd2wp_skip_blank_titles'] ); ?>
			/> <label for="url_redirect" ><?php esc_html_e( 'Nodes with blank titles will not be imported', 'fgd2wp' ); ?></label>
		</div>
	</td>
</tr>
