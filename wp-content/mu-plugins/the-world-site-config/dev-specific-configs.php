<?php
    # Set the WP-CFM environment
    add_filter( 'the_world_site_config', 'dev' );
    # Disable jetpack_development_mode
    add_filter( 'jetpack_development_mode', '__return_true' );
