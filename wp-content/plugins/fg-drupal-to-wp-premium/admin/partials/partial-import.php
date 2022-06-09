			<tr>
				<th scope="row"><?php _e('Partial import:', 'fgd2wpp'); ?></th>
				<td>
					<div id="partial_import_toggle"><?php _e('expand / collapse', 'fgd2wpp'); ?></div>
					<div id="partial_import">
					<input id="skip_taxonomies" name="skip_taxonomies" type="checkbox" value="1" <?php checked($data['skip_taxonomies'], 1); ?> /> <label for="skip_taxonomies" ><?php _e('Don\'t import the taxonomies', 'fgd2wpp'); ?></label>
					<br />
					<!-- DINKUM: Add skip taxonomies. -->
					<div id="skip_taxonomies_box">
						<small><a href="#" id="toggle_taxonomy_types"><?php _e('Select / Deselect all', 'fgd2wpp'); ?></a></small><br />
						<div id="partial_import_taxonomies"><?php echo $data['partial_import_taxonomies']; ?></div>
					</div>
					<br />
					<input id="skip_nodes" name="skip_nodes" type="checkbox" value="1" <?php checked($data['skip_nodes'], 1); ?> /> <label for="skip_nodes" ><?php _e('Don\'t import the nodes', 'fgd2wpp'); ?></label>
					<br />
					<div id="skip_nodes_box">
						<small><a href="#" id="toggle_node_types"><?php _e('Select / Deselect all', 'fgd2wpp'); ?></a></small><br />
						<div id="partial_import_nodes"><?php echo $data['partial_import_nodes']; ?></div>
					</div>
					<input id="skip_users" name="skip_users" type="checkbox" value="1" <?php checked($data['skip_users'], 1); ?> /> <label for="skip_users" ><?php _e('Don\'t import the users', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_menus" name="skip_menus" type="checkbox" value="1" <?php checked($data['skip_menus'], 1); ?> /> <label for="skip_menus" ><?php _e('Don\'t import the menus', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_comments" name="skip_comments" type="checkbox" value="1" <?php checked($data['skip_comments'], 1); ?> /> <label for="skip_comments" ><?php _e('Don\'t import the comments', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_blocks" name="skip_blocks" type="checkbox" value="1" <?php checked($data['skip_blocks'], 1); ?> /> <label for="skip_blocks" ><?php _e('Don\'t import the blocks', 'fgd2wpp'); ?></label>
					<br />
					<input id="skip_redirects" name="skip_redirects" type="checkbox" value="1" <?php checked($data['skip_redirects'], 1); ?> /> <label for="skip_redirects" ><?php _e('Don\'t import the redirects', 'fgd2wpp'); ?></label>
					<?php do_action('fgd2wp_post_display_partial_import_options', $data); ?>
					</div>
				</td>
			</tr>
