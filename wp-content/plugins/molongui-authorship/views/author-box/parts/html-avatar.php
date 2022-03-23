<?php
?>

<?php if ( $author['img'] and ( !empty( $options['show_avatar'] ) and $options['show_avatar'] ) ) : ?>
	<div class="m-a-box-item m-a-box-avatar <?php echo ( ( !empty( $options['profile_valign'] ) and $options['profile_valign'] != 'center' ) ? 'molongui-align-self-'.$options['profile_valign'] : '' ); ?>">
		<?php
            if ( !$options['avatar_link_to_archive'] or
                 ( $author['type'] === 'guest' and !authorship_has_pro() ) or
                 ( $author['type'] === 'guest' and !$options['guest_pages'] ) or
                 ( $author['type'] === 'user'  and !$options['user_archive_enabled'] ) )
            {
	            ?>
                <span>
                    <?php echo $author['img']; ?>
                </span>
                <?php
            }
            elseif ( is_customize_preview() and
	                 ( ( $author['type'] === 'guest' and $options['guest_pages'] and authorship_has_pro() ) or
	                   ( $author['type'] === 'user'  and $options['user_archive_enabled'] ) ) )
            {
                ?>
                <a href="#" class="customize-unpreviewable">
                    <?php echo $author['img']; ?>
                </a>
                <?php
            }
            else
            {
                ?>
                <a href="<?php echo esc_url( $author['archive'] ); ?>">
                    <?php echo $author['img']; ?>
                </a>
                <?php
            }
        ?>
	</div>
<?php endif; ?>