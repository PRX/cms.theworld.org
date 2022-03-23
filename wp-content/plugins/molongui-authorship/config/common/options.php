<?php
defined( 'ABSPATH' ) or exit;

$fw_options = array();
if ( apply_filters( 'authorship/options/add_common_main', true ) )
{
    $fw_options[] = array
    (
        'display' => true,
        'type'    => 'section',
        'id'      => 'more',
        'name'    => __( "More", 'molongui-authorship' ),
    );
    $fw_options[] = array
    (
        'display'  => true,
        'advanced' => false,
        'type'     => 'header',
        'class'    => 'hidden',
        'label'    => __( "Uninstall", 'molongui-authorship' ),
        'buttons'  => array(),
    );
    $fw_options[] = array
    (
        'display'  => true,
        'advanced' => false,
        'type'     => 'toggle',
        'class'    => 'hidden',
        'default'  => true,
        'id'       => 'keep_config',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sKeep this setting enabled to prevent config loss when removing the plugin from your site.%s %sKeeping plugin config might be useful on plugin reinstall or site migration.%s %sIf you want to completely remove all plugin config, uncheck this setting and then remove the plugin.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'    => __( "Keep plugin configuration for future use upon plugin uninstall.", 'molongui-authorship' ),
    );
    $fw_options[] = array
    (
        'display'  => true,
        'advanced' => false,
        'type'     => 'toggle',
        'class'    => 'hidden',
        'default'  => true,
        'id'       => 'keep_data',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sKeep this setting enabled to prevent data loss when removing the plugin from your site.%s %sKeeping plugin data might be useful on plugin reinstall or site migration.%s %sIf you want to completely remove any data added by the plugin since it was installed, uncheck this setting and then remove the plugin.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'    => __( "Keep plugin data for future use upon plugin uninstall.", 'molongui-authorship' ),
    );
}
$fw_options = apply_filters( 'authorship/common/options', $fw_options );