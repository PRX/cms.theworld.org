<?php
$name_text_style = '';
if ( !empty( $options['name_text_style'] ) )
{
	foreach ( explode(',', $options['name_text_style'] ) as $style ) $name_text_style .= ' molongui-text-style-'.$style;
}
$name_tag = ( !empty( $options['box_author_name_tag'] ) ? $options['box_author_name_tag'] : 'h5' );

?>

<div class="m-a-box-title">
	<<?php echo $name_tag; ?> class="molongui-font-size-<?php echo $options['name_text_size']; ?>-px
	        molongui-text-align-<?php echo $options['name_text_align']; ?>
            <?php echo $name_text_style; ?>
            molongui-text-case-<?php echo $options['name_text_case']; ?>"
            style="color: <?php echo $options['name_text_color']; ?>"
            <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>>
        <?php
            if ( !$options['name_link_to_archive'] or
	             ( $author['type'] === 'guest' and !authorship_has_pro() ) or
	             ( $author['type'] === 'guest' and !$options['guest_pages'] ) or
	             ( $author['type'] === 'user'  and !$options['user_archive_enabled'] ) )
            {
	            ?>
                <span class="molongui-font-size-<?php echo $options['name_text_size']; ?>-px molongui-text-align-<?php echo $options['name_text_align']; ?>" style="color:<?php echo$settings['name_text_color']; ?>;">
			        <?php echo $author['name']; ?>
                </span>
	            <?php
            }
            elseif ( is_customize_preview() and
                     ( ( $author['type'] === 'guest' and $options['guest_pages'] and authorship_has_pro() ) or
                       ( $author['type'] === 'user'  and $options['user_archive_enabled'] ) ) )
            {
	            ?>
                <a href="#" class="customize-unpreviewable <?php echo ( $options['name_inherited_underline'] == 'remove' ? 'molongui-remove-text-underline' : '' ); ?> molongui-font-size-<?php echo $options['name_text_size']; ?>-px molongui-text-align-<?php echo $options['name_text_align']; ?> <?php echo $name_text_style; ?>" style="color:<?php echo $options['name_text_color']; ?>;" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>>
		            <?php echo $author['name']; ?>
                </a>
	            <?php
            }
            else
            {
	            ?>
                <a href="<?php echo esc_url( $author['archive'] ); ?>" class="<?php echo ( $options['name_inherited_underline'] == 'remove' ? 'molongui-remove-text-underline' : '' ); ?> molongui-font-size-<?php echo $options['name_text_size']; ?>-px molongui-text-align-<?php echo $options['name_text_align']; ?> <?php echo $name_text_style; ?>" style="color:<?php echo $options['name_text_color']; ?>;" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>>
		            <?php echo $author['name']; ?>
                </a>
	            <?php
            }
        ?>
	</<?php echo $name_tag; ?>>
</div>