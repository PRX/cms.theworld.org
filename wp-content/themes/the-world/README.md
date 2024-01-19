# The World Wordpress Theme

This theme will update preview links to go to the appropriate route on the front-end, along with supplying authorization tokens for the front-end to use via HTTP cookies. The preview authorization only works when the WP admin and the front-end are served from the same domain, which may not be possible in pantheon environments.

## Required Plugins

### Faust.js

Used to redirect non-admin requests to frontend domain.

Can be installed from plugins page. Be sure to add its main file path to `wp-content/mu-plugins/the-world-site-config/configs/global/global-plugins.php`.

Settings Path: `/wp-admin/options-general.php?page=faustwp-settings`

> Pantheon environments that do not have a `theworld.org` domain setup do not need to be configured.

- Set **Front-end site URL** to base domain for the environment's front-end.
  - **Local Dev:** `http://the-world-wp.lndo.site:3000`
  - **Production:** `https://theworld.org`
- Check **Enable Post and Category URL rewrites**.
- Check **Enable public route redirects**.

### WPGraphQL JWT Authentication

Used to generate authorization tokens the front-end can use to request data for draft content.

Download latest release from [WP GraphQL GitHub](https://github.com/wp-graphql/wp-graphql-jwt-authentication/releases). Be sure to add its main file path to `wp-content/mu-plugins/the-world-site-config/configs/global/global-plugins.php`.

Create environment variable `TW_PREVIEWS_SECRET_KEY`. It's recommended that you use something like the WordPress Salt generator (https://api.wordpress.org/secret-key/1.1/salt/) to generate the secret key. In local dev, add it to your `.env`. For Pantheon environments, use Terminus secrets plugin to set the secret.
