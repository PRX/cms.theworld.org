<?php
$box_tabs = array
(
    'name' => 'mab-tabs-'.$random_id,
    'tabs' => array
    (
        'profile' => array
        (
            'id'       => 'mab-tab-profile-'.$random_id,
            'label'    => ( $options['about_the_author'] ? $options['about_the_author'] : __( 'About the author', 'molongui-authorship' ) ),
            'label_id' => 'm-a-box-string-about-the-author',
            'checked'  => true,
            'display'  => true,
        ),
        'related' => array
        (
            'id'       => 'mab-tab-related-'.$random_id,
            'label'    => ( $options['related_posts'] ? $options['related_posts'] : __( 'Related posts', 'molongui-authorship' ) ),
            'label_id' => 'm-a-box-string-related-posts',
            'checked'  => false,
            'display'  => $show_related,
        ),
        'contact' => array
        (
            'id'       => 'mab-tab-contact-'.$random_id,
            'label_id' => 'm-a-box-string-contact',
            'checked'  => false,
            'display'  => false,
        ),
    ),
);
if ( !empty( $options['tabs_position'] ) ) $position = explode('-', $options['tabs_position'] );
$nav_class  = '';
$nav_class .= ' m-a-box-tabs-'.$position[0];
$nav_class .= isset( $position[1] ) ? ' m-a-box-tabs-'.$position[1] : '';
$tab_class  = '';
$tab_class .= ( ( !empty( $options['tabs_border'] ) ) ? ' m-a-box-tab-border-'.$options['tabs_border'] : '' );
$active_class = 'm-a-box-tab-active';
?>

<script type="text/javascript" language="JavaScript">

	function molonguiHandleTab(myRadio)
	{
        var mabId = myRadio.id.slice( myRadio.id.lastIndexOf('-')+1 );
		document.querySelector( '#mab-'+mabId+' .m-a-box-tabs nav label.m-a-box-tab.<?php echo $active_class; ?>' ).classList.remove( '<?php echo $active_class; ?>' );
		document.querySelector( 'label[for='+myRadio.id+']' ).classList.add( '<?php echo $active_class; ?>' );
	}

</script>

<?php
foreach ( $box_tabs['tabs'] as $box_tab ) :
    if ( !$box_tab['display'] ) continue; ?>
    <input type="radio" id="<?php echo $box_tab['id']; ?>" name="<?php echo $box_tabs['name']; ?>" onclick="molonguiHandleTab(this);" <?php echo ( $box_tab['checked'] ? 'checked' : '' ); ?>>
<?php endforeach; ?>

<nav class="<?php echo $nav_class; ?>" style="<?php //echo $nav_style; ?>">
    <?php foreach ( $box_tabs['tabs'] as $box_tab )
    {
        if ( !$box_tab['display'] ) continue;
        ?>
            <label for="<?php echo $box_tab['id']; ?>"
                   class="m-a-box-tab <?php echo $tab_class; echo ( $box_tab['checked'] ? ' '.$active_class : '' ); ?>"
                   style="<?php echo ( ( !empty( $options['tabs_text_color'] ) ) ? 'color:'.$options['tabs_text_color'] : '' ); //echo $tab_style; echo ( $box_tab['checked'] ? $active_style : '' ); ?>">
                <span class="<?php echo $box_tab['label_id']; ?>"><?php echo $box_tab['label']; ?></span>
            </label>
        <?php
    }?>
</nav>