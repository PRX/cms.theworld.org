<?php
defined( 'ABSPATH' ) or exit;
function molongui_authorship_profile_customizer_preview( $author, $options, $random_id )
{
    $add_microdata = false;

	switch ( substr( $options['profile_layout'], 7 ) )
	{
        case '2':
        case '3':
        case '4':
        case '5':
		case '6':

            include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/profile/html-layout-1.php';

		break;

		case '7':
		case '8': ?>

            <div class="m-a-box-content-top"></div><!-- End of .m-a-box-content-top -->

            <div class="m-a-box-content-middle">
                <!-- Author picture -->
                <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-avatar.php'; ?>

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
						            if ( !empty( $author['posts'] ) )
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

            <div class="m-a-box-content-bottom molongui-border-style-<?php echo $options['bottom_border_style']; ?> molongui-border-right-none molongui-border-bottom-none molongui-border-left-none molongui-border-width-<?php echo $options['bottom_border_width']; ?>-px" style="background-color: <?php echo $options['bottom_background_color']; ?>; border-color: <?php echo $options['bottom_border_color']; ?>">
                <!-- Author social -->
                <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-socialmedia.php'; ?>
            </div><!-- End of .m-a-box-content-bottom -->

		<?php break;
	}
}
function molongui_authorship_related_customizer_preview( $author, $options, $random_id )
{
	switch ( substr( $options['related_layout'], 7 ) )
	{
        case '3':

            foreach( $author['posts'] as $related )
            {
            ?>
            <li>
                <div class="m-a-box-related-entry" itemscope itemtype="http://schema.org/CreativeWork">

                    <div class="molongui-display-none" itemprop="author" itemscope itemtype="http://schema.org/Person">
                        <div itemprop="name"><?php echo $author['name']; ?></div>
                        <div itemprop="url"><?php echo esc_url( $author['archive'] ); ?></div>
                    </div>

                    <!-- Related entry thumb -->
                    <div class="m-a-box-related-entry-thumb">
						<?php if ( has_post_thumbnail( $related->ID ) ) : ?>
                            <a href="<?php echo get_permalink( $related->ID ); ?>">
								<?php echo get_the_post_thumbnail( $related->ID, 'thumbnail', $attr = array( 'itemprop' => 'thumbnailUrl' ) ) ?>
                            </a>
						<?php else : ?>
                            <img src="<?php echo MOLONGUI_AUTHORSHIP_URL.'assets/img/related_placeholder.svg'; ?>" width="<?php echo get_option( 'thumbnail_size_w' ).'px'; ?>">
						<?php endif; ?>
                    </div>

                    <!-- Related entry date -->
                    <div class="m-a-box-related-entry-date" itemprop="datePublished">
						<?php echo get_the_date( '', $related->ID ); ?>
                    </div>

                    <!-- Related entry title -->
                    <div class="m-a-box-related-entry-title">
                        <a class="molongui-remove-text-underline" itemprop="url" href="<?php echo get_permalink( $related->ID ); ?>">
                            <span itemprop="headline"><?php echo $related->post_title; ?></span>
                        </a>
                    </div>

                </div>
            </li>
            <?php
            }

		break;
	}
}