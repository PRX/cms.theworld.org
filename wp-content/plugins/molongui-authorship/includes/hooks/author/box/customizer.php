<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_user_add_profile_fields' ) )
{
    function authorship_load_customizer()
    {
        return authorship_is_feature_enabled( 'box' ) and authorship_is_feature_enabled( 'box_styles' );
    }
    add_filter( 'authorship/load_customizer', 'authorship_load_customizer' );
}