<?php
defined( 'ABSPATH' ) or exit;
function authorship_add_settings_menu_items()
{
    $options = authorship_get_options();
    if ( !$options['settings_submenu'] ) return;
    $position = 2;
    if ( authorship_is_feature_enabled( 'box' ) )
    {
        add_options_page
        (
            '',
            _x( "Author Box", "Settings submenu title", 'molongui-authorship' ),
            'manage_options',
            'admin.php?page=molongui-authorship&tab=author-box',
            '',
            $position
        );

        $position++;
    }
    if ( authorship_is_feature_enabled( 'guest' ) )
    {
        add_options_page
        (
            '',
            _x( "Guest Authors", "Settings submenu title", 'molongui-authorship' ),
            'manage_options',
            'admin.php?page=molongui-authorship&tab=guest-authors',
            '',
            $position
        );

        $position++;
    }
    if ( authorship_is_feature_enabled( 'multi' ) )
    {
        add_options_page
        (
            '',
            _x( "Co-authors", "Settings submenu title", 'molongui-authorship' ),
            'manage_options',
            'admin.php?page=molongui-authorship&tab=co-authors',
            '',
            $position
        );

        $position++;
    }
}
add_action( 'admin_menu', 'authorship_add_settings_menu_items' );