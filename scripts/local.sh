echo "Import configuration from WP CFM bundles and Replace domain in the database."
lando wp plugin activate wp-cfm
lando wp config pull sample_bundle
lando wp --path=/app --url=dev-the-world-wp.pantheonsite.io search-replace 'dev-the-world-wp.pantheonsite.io' 'the-world-wp.lndo.site'
