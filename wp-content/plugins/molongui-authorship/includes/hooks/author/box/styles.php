<?php
defined( 'ABSPATH' ) or exit;
function authorship_register_box_styles()
{
    $file = apply_filters( 'authorship/box/styles', MOLONGUI_AUTHORSHIP_FOLDER . ( is_rtl() ? '/assets/css/author-box-rtl.49bc.min.css' : '/assets/css/author-box.33a6.min.css' ) );

    authorship_register_style( $file, 'box' );
}
add_action( 'wp_enqueue_scripts', 'authorship_register_box_styles' );
function authorship_enqueue_box_styles()
{
    if ( !authorship_is_feature_enabled( 'box' ) or !authorship_is_feature_enabled( 'box_styles' ) ) return;
    $file = apply_filters( 'authorship/box/styles', MOLONGUI_AUTHORSHIP_FOLDER . ( is_rtl() ? '/assets/css/author-box-rtl.49bc.min.css' : '/assets/css/author-box.33a6.min.css' ) );

    authorship_enqueue_style( $file, 'box' );
}
function authorship_box_extra_styles()
{
    $options = authorship_get_options();
    $css = '';
    $bp  = empty( $options['breakpoint'] ) ? '600' : $options['breakpoint'];
    $css .= ":root{ --m-a-box-bp: " . $bp . "px; --m-a-box-bp-l: " . --$bp . "px; }";
    if ( $options['enable_cdn_compat'] )
    {
        $bp_low_limit = $bp - 1;
        $item_spacing = '20';
        $eqcss        = '';
        $eqcss .= '.m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content .m-a-box-content-top,
                   .m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content .m-a-box-content-middle,
                   .m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content .m-a-box-content-bottom { flex-direction: row; flex-wrap: nowrap; }
                  ';
        $eqcss .= '.m-a-box-container[max-width~="'.$bp_low_limit.'px"] .m-a-box-title > :first-child { text-align: center !important; }
                   .m-a-box-container[max-width~="'.$bp_low_limit.'px"] .m-a-box-meta { text-align: center !important; }
                  ';

        $eqcss .= '.m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content.m-a-box-profile .m-a-box-avatar { flex: 0 0 auto; align-self: center; padding: 0 '.$item_spacing.'px 0 0; min-width: auto; }
                   .m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content.m-a-box-profile .m-a-box-social { display: flex; flex-direction: column; margin-top: 0; padding: 0 '.$item_spacing.'px 0 0; }     
                   .m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content.m-a-box-profile .m-a-box-data { flex: 1 0; margin-top: 0; }     
                   .m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content.m-a-box-profile .m-a-box-data .m-a-box-title > :first-child { text-align: left; }     
                   .m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content.m-a-box-profile .m-a-box-data .m-a-box-meta { text-align: left; }     
                  ';
        $eqcss .= '.m-a-box-container[min-width~="'.$bp.'px"] .m-a-box-content .m-a-box-social .m-a-box-social-icon { margin: 0.4em 0; }';
        $eqcss = apply_filters( 'authorship/eqcss/fallback', $eqcss, $bp, $item_spacing );

        $css .= $eqcss;
    }
    if ( 'tabbed' === $options['box_layout'] )
    {
        if ( !empty( $options['tabs_position'] ) ) $position = explode('-', $options['tabs_position'] );
        if ( !empty( $position[0] ) ) $position = $position[0];
        else $position = 'top';
        if ( !empty( $options['tabs_border'] ) ) $border = $options['tabs_border'];
        else $border = 'around';
        $nav_style    = '';
        $tab_style    = '';
        $active_style = '';
        if ( !empty( $options['tabs_background'] ) ) $nav_style .= ' background-color:'.$options['tabs_background'].';';
        if ( !empty( $options['tabs_color'] ) )      $tab_style .= ' background-color:'.$options['tabs_color'].';';
        $tabs_background_style        = 'background-color: '.$options['tabs_color'].';';
        $tabs_active_background_style = 'background-color: '.$options['tabs_active_color'].';';
        $css .= "
                .m-a-box .m-a-box-tabs nav.m-a-box-tabs-{$position} { {$nav_style} }
                .m-a-box .m-a-box-tabs nav label { {$tab_style} }
                .m-a-box .m-a-box-tabs input[id^='mab-tab-profile-']:checked ~ nav label[for^='mab-tab-profile-'],
                .m-a-box .m-a-box-tabs input[id^='mab-tab-related-']:checked ~ nav label[for^='mab-tab-related-'],
                .m-a-box .m-a-box-tabs input[id^='mab-tab-contact-']:checked ~ nav label[for^='mab-tab-contact-']
                {
                    {$active_style}
                }
                
                .m-a-box .m-a-box-tabs nav label.m-a-box-tab { {$tabs_background_style} }
                .m-a-box .m-a-box-tabs nav label.m-a-box-tab.m-a-box-tab-active { {$tabs_active_background_style} }
                
                .m-a-box .m-a-box-tabs .m-a-box-related .m-a-box-related-entry-title,
                .m-a-box .m-a-box-tabs .m-a-box-related .m-a-box-related-entry-title a
                {
                    color: {$options['related_text_color']};
                }
            ";
    }
    return apply_filters( 'authorship/box/extra_styles', $css, $options );
}
function authorship_box_update_font_path( $contents )
{
    return str_replace( "url('../font/molongui-authorship-font.", "url('".MOLONGUI_AUTHORSHIP_URL."assets/font/molongui-authorship-font.", $contents );
}
add_filter( '_authorship/box/styles_contents', 'authorship_box_update_font_path' );