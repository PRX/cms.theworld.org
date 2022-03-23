<?php
?>

<div class="m-a-box-content-top">

	<?php if ( $options['box_layout'] == 'stacked' ) include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-profile-title.php'; ?>

</div><!-- End of .m-a-box-content-top -->

<div class="m-a-box-content-middle">

    <!-- Author picture -->
    <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-avatar.php'; ?>

    <!-- Author social -->
    <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-socialmedia.php'; ?>

    <!-- Author data -->
    <div class="m-a-box-item m-a-box-data <?php echo ( ( !empty( $options['profile_valign'] ) and $options['profile_valign'] != 'center' ) ? 'molongui-align-self-'.$options['profile_valign'] : '' ); ?>">

        <!-- Author name -->
        <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-name.php'; ?>

        <!-- Author metadata -->
        <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-meta.php'; ?>

        <!-- Author bio -->
        <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-bio.php'; ?>

        <?php if ( $options['box_layout'] == 'slim' and !empty( $options['show_related'] ) ) : ?>

            <!-- Author related posts -->
            <div class="m-a-box-related" data-related-layout="<?php echo $options['related_layout']; ?>">
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
                                require_once MOLONGUI_AUTHORSHIP_DIR . 'customizer/plugin-customizer-preview.php';
                                molongui_authorship_related_customizer_preview( $author, $options, $random_id );
                            }
                        }
                        else
                        {
                            echo ' <span class="m-a-box-string-no-related-posts">'. ( $options[ 'no_related_posts' ] ? $options[ 'no_related_posts' ] : __( 'This author does not have any more posts.', 'molongui-authorship' ) ).'</span>';
                        }
                        ?>
                    </ul>

                </div><!-- End of .m-a-box-related-entries -->
            </div><!-- End of .m-a-box-related -->

        <?php endif; ?>

    </div><!-- End of .m-a-box-data -->

</div><!-- End of .m-a-box-content-middle -->

<div class="m-a-box-content-bottom"></div><!-- End of .m-a-box-content-bottom -->