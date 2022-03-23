<?php
$nofollow = ( $options['add_nofollow'] ? 'rel="nofollow"' : '' );
$meta_text_style = '';
if ( !empty( $options['meta_text_style'] ) )
{
	foreach ( explode(',', $options['meta_text_style'] ) as $style ) $meta_text_style .= ' molongui-text-style-'.$style;
}
$add_separator = false;

?>

<?php if ( !isset( $options['show_meta'] ) or !empty( $options['show_meta'] ) ) : ?>
<div class="m-a-box-item m-a-box-meta
            molongui-font-size-<?php echo $options['meta_text_size']; ?>-px
            molongui-text-align-<?php echo $options['meta_text_align']; ?>
            <?php echo $meta_text_style; ?>
            molongui-text-case-<?php echo $options['meta_text_case']; ?>"
     style="color: <?php echo $options['meta_text_color']; ?>">

    <?php if ( $author['job'] ) : ?>
        <span <?php echo ( $add_microdata ? 'itemprop="jobTitle"' : '' ); ?>><?php echo $author['job']; ?></span>
        <?php $add_separator = true; ?>
    <?php endif; ?>

    <?php if ( $author['job'] and $author['company'] ) echo ' <span class="m-a-box-string-at">'.( $options['at'] ? $options['at'] : __( "at", 'molongui-authorship' ) ).'</span> '; ?>

    <?php if ( $author['company'] ) : ?>
        <span <?php echo ( $add_microdata ? 'itemprop="worksFor" itemscope itemtype="https://schema.org/Organization"' : '' ); ?>>
            <?php if ( $author['company_link'] ) echo '<a href="' . esc_url( $author['company_link'] ) . '" target="_blank" '.( $add_microdata ? 'itemprop="url"' : '' ).' style="color: '.$options['meta_text_color'].';" ' . $nofollow . '>'; ?>
            <span <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>><?php echo $author['company']; ?></span>
            <?php if ( $author['company_link'] ) echo '</a>'; ?>
        </span>
        <?php $add_separator = true; ?>
    <?php endif; ?>

	<?php if ( $author['phone'] and $author['show_meta_phone'] ) : ?>
		<?php
            $phone_item = '<a href="tel:'.$author['phone'].'"'. ( $add_microdata ? ' itemprop="telephone"' : '' ) . ' content="'.$author['phone'].'" style="color:'.$options['meta_text_color'].'" '.$nofollow.'>' . $author['phone'] . '</a>';
            $phone_item = apply_filters( 'authorship/box/meta/phone', $phone_item, $author['phone'], $add_microdata, $nofollow );
        ?>
		<?php if ( $add_separator ) echo ' '.'<span class="m-a-box-meta-separator">'.$options['meta_separator'].'</span>'.' '; ?>
        <?php echo $phone_item; ?>
        <?php $add_separator = true; ?>
	<?php endif; ?>

	<?php if ( $author['mail'] and $author['show_meta_mail'] ) : ?>
        <?php
            $email_item = '<a href="mailto:'.$author['mail'].'" target="_top"'. ( $add_microdata ? ' itemprop="email"' : '' ) . ' content="'.$author['mail'].'" style="color:'.$options['meta_text_color'].'" '.$nofollow.'>' . $author['mail'] . '</a>';
            $email_item = apply_filters( 'authorship/box/meta/email', $email_item, $author['mail'], $add_microdata, $nofollow );
        ?>
        <?php if ( $add_separator ) echo ' '.'<span class="m-a-box-meta-separator">'.$options['meta_separator'].'</span>'.' '; ?>
        <?php echo $email_item; ?>
        <?php $add_separator = true; ?>
	<?php endif; ?>

	<?php if ( $author['web'] ) : ?>
        <?php if ( $add_separator ) echo ' '.'<span class="m-a-box-meta-separator">'.$options['meta_separator'].'</span>'.' '; ?>
		<a href="<?php echo esc_url( $author['web'] ); ?>" target="_blank" style="color: <?php echo $options['meta_text_color']; ?>" <?php echo $nofollow; ?>><?php echo ' <span class="m-a-box-string-web">'.( $options['web'] ? apply_filters( 'authorship/box/meta/web', $options['web'], $author ) : __( 'Website', 'molongui-authorship' ) ).'</span>'; ?></a>
        <?php $add_separator = true; ?>
	<?php endif; ?>

	<?php if ( $options['box_layout'] == 'slim' and $options['show_related'] and ( !empty( $author['posts'] ) or !empty( $options['show_empty_related'] ) ) ) : ?>
        <?php if ( $add_separator ) echo ' '.'<span class="m-a-box-meta-separator">'.$options['meta_separator'].'</span>'.' '; ?>
		<script type="text/javascript" language="JavaScript">
			if ( typeof window.ToggleAuthorshipData === 'undefined' )
			{
				function ToggleAuthorshipData(id, author)
				{
					var box_selector = '#mab-' + id;
                    var box = document.querySelector(box_selector);
                    if ( box.getAttribute('data-multiauthor') ) box_selector = '#mab-' + id + ' [data-author-ref="' + author + '"]';
					var label = document.querySelector(box_selector + ' ' + '.m-a-box-data-toggle');
					label.innerHTML = ( label.text.trim() === "<?php echo ( $options['more_posts'] ? apply_filters( 'authorship/box/meta/more', $options['more_posts'], $author ) : __( '+ posts', 'molongui-authorship' ) ); ?>" ? " <span class=\"m-a-box-string-bio\"><?php echo ( $options['bio'] ? apply_filters( 'authorship/box/meta/bio', $options['bio'], $author ) : __( 'Bio', 'molongui-authorship' ) ); ?></span>" : " <span class=\"m-a-box-string-more-posts\"><?php echo ( $options['more_posts'] ? apply_filters( 'authorship/box/meta/more', $options['more_posts'], $author ) : __( '+ posts', 'molongui-authorship' ) ); ?></span>" );
					var bio     = document.querySelector(box_selector + ' ' + '.m-a-box-bio');
					var related = document.querySelector(box_selector + ' ' + '.m-a-box-related-entries');

					if ( related.style.display === "none" )
					{
						related.style.display = "block";
						bio.style.display     = "none";
					}
					else
					{
						related.style.display = "none";
						bio.style.display     = "block";
					}
				}
			}
		</script>
		<a href="javascript:ToggleAuthorshipData(<?php echo $random_id; ?>, '<?php echo $author['type'].'-'.$author['id']; ?>')" class="m-a-box-data-toggle" style="color: <?php echo $options['meta_text_color']; ?>" <?php echo $nofollow; ?>><?php echo ' <span class="m-a-box-string-more-posts">'.( $options[ 'more_posts' ] ? apply_filters( 'authorship/box/meta/more', $options['more_posts'], $author ) : __( '+ posts', 'molongui-authorship' ) ).'</span> '; ?></a>
	<?php endif; ?>

</div><!-- End of .m-a-box-meta -->
<?php endif; ?>