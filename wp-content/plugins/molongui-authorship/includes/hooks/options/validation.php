<?php
defined( 'ABSPATH' ) or exit;
function authorship_validate_freemium_options( $options, $current )
{
    $post_types = explode( ",", $options['post_types'] );
    $options['post_types'] = "";
    if ( in_array( 'post', $post_types ) ) $options['post_types'] .= "post,";
    if ( in_array( 'page', $post_types ) ) $options['post_types'] .= "page,";
    $options['post_types'] = rtrim( $options['post_types'], ',' );

    return $options;
}
add_filter( 'authorship/validate_options', 'authorship_validate_freemium_options', 10, 2 );
function authorship_validate_customizer_options( $options, $current )
{
    $customizer_settings = array
    (
        'box_margin',
        'box_width',
        'box_border',
        'box_border_style',
        'box_border_width',
        'box_border_color',
        'box_background',
        'box_shadow',
        'headline_text_size',
        'headline_text_style',
        'headline_text_case',
        'headline_text_align',
        'headline_text_color',
        'tabs_position',
        'tabs_background',
        'tabs_color',
        'tabs_active_color',
        'tabs_border',
        'tabs_border_style',
        'tabs_border_width',
        'tabs_border_color',
        'tabs_text_color',
        'profile_layout',
        'profile_valign',
        'profile_title',
        'bottom_background_color',
        'bottom_border_style',
        'bottom_border_width',
        'bottom_border_color',
        'avatar_style',
        'avatar_border_style',
        'avatar_border_width',
        'avatar_border_color',
        'avatar_text_color',
        'avatar_bg_color',
        'name_text_size',
        'name_text_style',
        'name_text_case',
        'name_text_align',
        'name_inherited_underline',
        'name_text_color',
        'meta_text_size',
        'meta_text_style',
        'meta_text_case',
        'meta_text_align',
        'meta_text_color',
        'meta_separator',
        'bio_text_size',
        'bio_line_height',
        'bio_text_style',
        'bio_text_align',
        'bio_text_color',
        'icons_style',
        'icons_size',
        'icons_color',
        'related_layout',
        'related_title',
        'related_text_size',
        'related_text_style',
        'related_text_case',
        'related_text_color',
    );
    foreach ( $customizer_settings as $customizer_setting )
    {
        if ( !isset( $options[$customizer_setting] ) and isset( $current[$customizer_setting] ) )
        {
            $options[$customizer_setting] = $current[$customizer_setting];
        }
    }

    return $options;
}
add_filter( 'authorship/validate_options', 'authorship_validate_customizer_options', 10, 2 );
function authorship_validate_options( $options, $current )
{
    if ( empty( $options['avatar_width'] ) ) $options['avatar_width'] = 150;
    if ( empty( $options['avatar_height'] ) ) $options['avatar_height'] = 150;
    if ( isset( $options['byline_multiauthor_separator']      ) ) $options['byline_multiauthor_separator']      = str_replace( array( "*", "?", "/" ), "", trim( $options['byline_multiauthor_separator'] ) );
    if ( isset( $options['byline_multiauthor_last_separator'] ) ) $options['byline_multiauthor_last_separator'] = str_replace( array( "*", "?", "/" ), "", trim( $options['byline_multiauthor_last_separator'] ) );

    return $options;
}
add_filter( 'authorship/validate_options', 'authorship_validate_options', 10, 2 );
add_action( 'authorship/options', 'authorship_add_defaults' );
function authorship_validate_refresh_options( $value, $old_value, $option )
{
    $refresh_premium_settings = array
    (
        'profile_layout' => array
        (
            'accepted' => array( 'layout-1' ),
            'default'  => 'layout-1',
        ),
        'related_layout' => array
        (
            'accepted' => array( 'layout-1', 'layout-2' ),
            'default'  => 'layout-1',
        ),
        'avatar_default_gravatar' => array
        (
            'accepted' => array( 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank' ),
            'default'  => 'mp',
        ),
        'bio_field' => array
        (
            'accepted' => array( 'full' ),
            'default'  => 'full',
        ),
    );
    foreach ( $refresh_premium_settings as $setting => $params )
    {
        if ( !isset( $value[$setting] ) )
        {
            $value[$setting] = $params['default'];
        }
        elseif ( !in_array( $value[$setting], $params['accepted'] ) )
        {
            if ( !empty( $old_value[$setting] ) and in_array( $old_value[$setting], $params['accepted'] ) ) $value[$setting] = $old_value[$setting];
            else $value[$setting] = $params['default'];
        }
    }

    return $value;
}
add_filter( "pre_update_option_molongui_authorship_options", 'authorship_validate_refresh_options', 10, 3 );
function authorship_validate_customize_changeset( $data, $filter_context )
{
    if ( 'publish' !== $filter_context['status'] ) return $data;
    $refresh_premium_settings = array
    (
        'profile_layout' => array
        (
            'accepted' => array( 'layout-1' ),
            'default'  => 'layout-1',
        ),
        'related_layout' => array
        (
            'accepted' => array( 'layout-1', 'layout-2' ),
            'default'  => 'layout-1',
        ),
        'avatar_default_gravatar' => array
        (
            'accepted' => array( 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank' ),
            'default'  => 'mp',
        ),
        'bio_field' => array
        (
            'accepted' => array( 'full' ),
            'default'  => 'full',
        ),
    );
    $current = authorship_get_options();

    foreach ( $data as $changed_option => $item )
    {
        $option = strtr( $changed_option, array( 'molongui_authorship_options[' => '', ']' => '' ) );
        if ( !in_array( $option, array_keys( $refresh_premium_settings ) ) ) continue;
        if ( !in_array( $item['value'], $refresh_premium_settings[$option]['accepted'] ) )
        {
            if ( !empty( $current[$option] ) and in_array( $current[$option], $refresh_premium_settings[$option]['accepted'] ) ) $data[$changed_option]['value'] = $current[$option];
            else $data[$changed_option]['value'] = $refresh_premium_settings[$option]['default'];
        }
    }

    return $data;
}
add_filter( 'customize_changeset_save_data', 'authorship_validate_customize_changeset', 10, 2 );