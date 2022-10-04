<?php
/*
 Description: Set up config constants.
 Author: Rick Peterman
*/

/**
 * Name of server platform. Used to load config files specific to that platform.
 */
define('SERVER_PLATFORM_NAME', 'pantheon');

/**
 * Name of the environment variable that acts as a flag for the platform server.
 */
define('SERVER_PLATFORM_ENVIRONMENT_VARIABLE_NAME', 'PANTHEON_ENVIRONMENT');

/**
 * Wire up S3 Uploads key and secret values to ENV variable.
 */
define( 'S3_UPLOADS_KEY', getenv('S3_KEY') );
define( 'S3_UPLOADS_SECRET', getenv('S3_SECRET') );
