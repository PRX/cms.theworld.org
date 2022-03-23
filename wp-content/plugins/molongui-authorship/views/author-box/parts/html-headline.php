<?php
$headline_text_style = '';
if ( !empty( $options['headline_text_style'] ) )
{
    foreach ( explode(',', $options['headline_text_style'] ) as $style ) $headline_text_style .= ' molongui-text-style-'.$style;
}
$headline_tag = ( !empty( $options['box_headline_tag'] ) ? $options['box_headline_tag'] : 'h3' );
?>

<?php if ( !empty( $options['show_headline'] ) and $options['show_headline'] == 1  ) : ?>
    <!-- Author headline -->
    <div class="m-a-box-item m-a-box-headline">
        <<?php echo $headline_tag; ?> class="molongui-font-size-<?php echo $options['headline_text_size']; ?>-px
                                             molongui-text-align-<?php echo $options['headline_text_align']; ?>
                                             <?php echo $headline_text_style; ?>
                                             molongui-text-case-<?php echo $options['headline_text_case']; ?>"
            style="color: <?php echo $options['headline_text_color']; ?>">
            <span class="m-a-box-string-headline"><?php echo ( $options['headline'] ? $options['headline'] : __( 'About the author', 'molongui-authorship' ) ); ?></span>
        </<?php echo $headline_tag; ?>>
    </div>
<?php endif; ?>