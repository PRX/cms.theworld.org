echo "Activate Configuration Management before importing configuration"
lando wp plugin activate wp-cfm
lando wp plugin activate wp-cfm-path
echo "Import configuration from WP CFM bundles and Replace domain in the database."
lando wp config pull all
