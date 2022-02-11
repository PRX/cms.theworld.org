<?php

/**
 * @param array $environments - Default is an empty array [] meaning multi environment config is disabled.
 * @return array
 */
function enable_multi_env( $environments ) {
    // Define an array containing the hosting environment names.
    // Or detect these with your own code logic if all are available in `$_ENV` or `$_SERVER` super-globals.
    // ...

    $environments = [
        'dev',
        'stage',
        'prod'
    ];

    return $environments;
}
add_filter( 'wpcfm_multi_env', 'enable_multi_env' );

/**
 * @param string $env - Default is an empty string ''.
 * @return string
 */
function set_current_env( $env ) {
    // Detect with your own code logic the current environment the WordPress site is running.
    // Generally this will be defined in a constant inside `$_ENV` or `$_SERVER` super-globals.
    // ...

    $env = 'dev';

    return $env;
}
add_filter( 'wpcfm_current_env', 'set_current_env' );

/**
 * @param string $config_dir - Default is "<root>/wp-content/config"
 * @return string
 */
function change_config_dir( $config_dir ) {
    // Change default path to $config_dir
    // ...

    $config_dir = WP_HOME . '/wp-content/config';

    return $config_dir;
}
add_filter( 'wpcfm_config_dir', 'change_config_dir' );

/**
 * @param string $config_url - Default is "<domain>/wp-content/config"
 * @return string
 */
function change_config_url( $config_url ) {
    // Change default URL to $config_url
    // ...

    $config_url = WP_HOME . '/wp-content/config';

    return $config_url;
}
add_filter( 'wpcfm_config_url', 'change_config_url' );
