ZIPFILE=`find ./reference/ -maxdepth 1 -name "*.sql.gz"`
SQLFILE=`find ./reference/ -maxdepth 1 -name "*.sql"`

if [ ${#ZIPFILE} -gt 0 ];
 then
  echo "Import local $ZIPFILE"
  lando db-import $ZIPFILE
elif [ ${#SQLFILE} -gt 0 ];
 then
  echo "Import local $SQLFILE"
  lando db-import $SQLFILE
else
  echo "Import Pantheon Dev database"
  lando pull --database=dev --files=none --code=none
fi

echo "Activate Configuration Management before importing configuration"
lando wp plugin activate wp-cfm
lando wp plugin activate wp-cfm-path
echo "Import configuration from WP CFM bundles and Replace domain in the database."
lando wp config pull all
echo "Replace any traces of a non-local domain in the database with the Lando domain"
lando wp --path=/app --url=*-the-world-wp.pantheonsite.io search-replace '*-the-world-wp.pantheonsite.io' 'the-world-wp.lndo.site'

echo "Create local admin user"
lando wp user create local-admin local-admin@the-world-wp.com
lando wp user set-role local-admin administrator
