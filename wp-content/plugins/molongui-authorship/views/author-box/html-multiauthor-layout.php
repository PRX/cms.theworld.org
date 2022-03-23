<?php
?>

<!-- MOLONGUI AUTHORSHIP PLUGIN <?php echo MOLONGUI_AUTHORSHIP_VERSION; ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB; ?> -->
<div class="molongui-clearfix"></div>
<div id="mab-<?php echo $random_id; ?>"
     class="m-a-box <?php echo ( isset( $options['box_class'] ) ? $options['box_class'] : '' ); ?>"
    <?php echo ( apply_filters( '_authorship/doing_shortcode/author_box', false ) ? 'data-is-shortcode="yes"' : '' ); ?>
     data-plugin-release="<?php echo MOLONGUI_AUTHORSHIP_VERSION; ?>"
     data-plugin-version="<?php echo authorship_has_pro() ? 'pro' : 'lite'; ?>"
     data-box-layout="<?php echo ( isset( $options['box_layout'] ) ? $options['box_layout'] : '' ); ?>"
     data-box-position="<?php echo ( isset( $options['box_position'] ) ? $options['box_position'] : '' ); ?>"
     data-multiauthor="<?php echo ( $is_multiauthor ? 'true' : 'false' ); ?>"
     data-authors-count="<?php echo count( $authors ); ?>"
     style="<?php echo ( ( !empty( $options['box_width'] ) )  ? 'width: '  . $options['box_width']  . '%;' : '' );?>
     <?php echo ( ( !empty( $options['box_margin'] ) ) ? 'margin: ' . $options['box_margin'] . 'px auto;' : '' );?>">

	<?php
	if ( $show_headline and !empty( $options['show_headline'] ) and $options['show_headline'] == 1  )
	{
		include( MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-headline.php' );
	}
	if ( $show_tabs = ( !empty( $options['box_layout'] ) and $options['box_layout'] == 'tabbed' ) )
	{
		echo '<div class="m-a-box-tabs">';
		include( MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-tabs.php' );
	}

	?>

    <div class="m-a-box-container
                <?php echo authorship_box_border( $options['box_border'] ); ?>
                molongui-border-style-<?php echo ( ( !empty( $options['box_border_style'] ) ) ? $options['box_border_style'] : 'none' ); ?>
                molongui-border-width-<?php echo ( ( !empty( $options['box_border_width'] ) ) ? $options['box_border_width'] : '0' ); ?>-px
                m-a-box-shadow-<?php echo ( ( !empty( $options['box_shadow'] ) ) ? $options['box_shadow'] : 'left' ); ?>"
         style="<?php echo ( ( !empty( $options['box_border_color'] ) ) ? 'border-color: ' . $options['box_border_color'] . ';' : '' );?>
	     <?php echo ( ( !empty( $options['box_background'] ) ) ? 'background-color: ' . $options['box_background'] . ';' : '' );?>">

        <div class="m-a-box-tab m-a-box-content m-a-box-profile"
             data-profile-layout="<?php echo $options['profile_layout']; ?>">

            <?php foreach ( $authors as $author ) : ?>

                <?php if ( isset( $author['hide'] ) and $author['hide'] ) continue; ?>

                <div class="m-a-box-profile-multiauthor" data-author-type="<?php echo $author['type']; ?>" data-author-ref="<?php echo $author['type'].'-'.$author['id']; ?>"
                     <?php echo ( $add_microdata ? 'itemscope itemid="'.$author['archive'].'" itemtype="https://schema.org/Person"' : '' ); ?>
                >
                    <?php
                    if ( !isset( $options['profile_layout'] ) or empty( $options['profile_layout'] ) or $options['profile_layout'] == 'layout-1' )
                    {
                        include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/profile/html-layout-1.php';
                    }
                    elseif ( authorship_has_pro() )
                    {
                        include MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/author-box/profile/html-'.$options['profile_layout'].'.php';
                    }
                    elseif ( is_customize_preview() )
                    {
                        require_once( MOLONGUI_AUTHORSHIP_DIR . 'customizer/plugin-customizer-preview.php' );
                        molongui_authorship_profile_customizer_preview( $author, $options, $random_id );
                    }
                    ?>
                </div>

            <?php endforeach; ?>

        </div><!-- End of .m-a-box-profile -->

        <?php if ( $options['box_layout'] != 'slim' and !empty($options['show_related']) and $options['show_related'] == 1 ) : ?>

            <?php $author['posts'] = get_coauthored_posts( $post_authors, false, array(), 'selected' ); ?>

            <div class="m-a-box-tab m-a-box-content m-a-box-related" data-related-layout="<?php echo $options['related_layout']; ?>">

                <div class="m-a-box-content-top">

			        <?php if ( $options['box_layout'] == 'stacked' ) include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-related-title.php'; ?>

                </div><!-- End of .m-a-box-content-top -->

                <div class="m-a-box-content-middle">

                    <!-- Related entries -->
                    <div class="m-a-box-item m-a-box-related-entries" <?php echo ( $options['box_layout'] == 'slim' ? 'style="display: none;"' : '' ); ?>>

				        <?php
				        $related_text_style = '';
				        if ( !empty( $options['related_text_style'] ) )
				        {
					        foreach ( explode(',', $options['related_text_style'] ) as $style ) $related_text_style .= ' molongui-text-style-'.$style;
				        }
				        ?>
                        <ul class="molongui-font-size-<?php echo $options['related_text_size']; ?>-px <?php echo $related_text_style; ?>">
					        <?php
					        if ( !empty( $author['posts'] ) ) //or is_array( $author_posts ) or is_object( $author_posts ) )
					        {
						        $premium_layouts = array( 'layout-3' );
						        if ( !isset( $options['related_layout'] ) or empty( $options['related_layout'] ) or $options['related_layout'] == 'layout-1' )
						        {
							        include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/related/html-layout-1.php';
						        }
                                elseif ( isset( $options['related_layout'] ) and !empty( $options['related_layout'] ) and !in_array( $options['related_layout'], $premium_layouts ) )
						        {
							        include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/related/html-'.$options['related_layout'].'.php';
						        }
                                elseif ( authorship_has_pro() and in_array( $options['related_layout'], $premium_layouts ) )
						        {
							        include MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/author-box/related/html-'.$options['related_layout'].'.php';
						        }
                                elseif ( is_customize_preview() )
						        {
							        require_once( MOLONGUI_AUTHORSHIP_DIR . 'customizer/plugin-customizer-preview.php' );
							        molongui_authorship_related_customizer_preview( $author, $options, $random_id );
						        }
					        }
					        else
					        {
						        echo ' <span class="m-a-box-string-no-related-posts">'. ( $options[ 'no_related_posts' ] ? $options[ 'no_related_posts' ] : __( "These authors do not have any more posts in common.", 'molongui-authorship' ) ).'</span>';
					        }
					        ?>
                        </ul>

                    </div><!-- End of .m-a-box-related-entries -->

                </div><!-- End of .m-a-box-content-middle -->

                <div class="m-a-box-content-bottom"></div><!-- End of .m-a-box-content-bottom -->

            </div><!-- End of .m-a-box-related -->

        <?php endif; ?>

    </div><!-- End of .m-a-box-container -->

	<?php if ( $show_tabs ) echo '</div><!-- End of .m-a-box-tabs -->'; ?>

</div><!-- End of .m-a-box -->