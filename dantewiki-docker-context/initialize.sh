#!/bin/bash
#
# File, which is part of the container and may be called from outside the container for doing the initialization / setup work for mediawiki.
#

# exit immediately if an error occurs
##   set -e

# The environment is provided from the outside. Nevertheless we set some sane defaults.
: ${MEDIAWIKI_SITE_NAME:=Dantepedia}
: ${MEDIAWIKI_SITE_LANG:=en}
: ${MEDIAWIKI_ADMIN_USER:=admin}
: ${MEDIAWIKI_ENABLE_SSL:=false}
: ${MEDIAWIKI_RUN_UPDATE_SCRIPT:=true}

: ${MEDIAWIKI_DB_PORT:=3306}

: ${MEDIAWIKI_DB_NAME:=mediawiki}

: ${MEDIAWIKI_DB_TYPE:=mysql}





## WAIT for the DATABASE container
while [ `/bin/nc -w 1 $MEDIAWIKI_DB_HOST $MEDIAWIKI_DB_PORT < /dev/null > /dev/null; echo $?` != 0 ]; do
    echo "Waiting for database to come up at $MEDIAWIKI_DB_HOST:$MEDIAWIKI_DB_PORT...may take up to a minute..."
    sleep 1
done

export MEDIAWIKI_DB_TYPE MEDIAWIKI_DB_HOST MEDIAWIKI_DB_USER MEDIAWIKI_DB_PASSWORD MEDIAWIKI_DB_NAME


## GENERATE database unless it already 

echo " -------------------------------"
echo "  "
echo "GENERATING DATABASE NOW...V2... ${MEDIAWIKI_DB_PASSWORD}"
echo "  "

TERM=dumb php -- <<'EOPHP'
<?php
  $mysql = new mysqli($_ENV['MEDIAWIKI_DB_HOST'], $_ENV['MEDIAWIKI_DB_USER'], $_ENV['MEDIAWIKI_DB_PASSWORD'], '', (int) $_ENV['MEDIAWIKI_DB_PORT']);
  if ($mysql->connect_error) {
    file_put_contents('php://stderr', 'MySQL Connection Error: (' . $mysql->connect_errno . ') ' . $mysql->connect_error . "\n");
    exit(1);
  }
  if (!$mysql->query('CREATE DATABASE IF NOT EXISTS `' . $mysql->real_escape_string($_ENV['MEDIAWIKI_DB_NAME']) . '`') ) {
    file_put_contents('php://stderr', 'MySQL "CREATE DATABASE" Error: ' . $mysql->error . "\n");
    $mysql->close();
    exit(1);
  }
  $cmd = "GRANT ALL ON *.* TO 'root'@'localhost'";
  file_put_contents ('php:stderr', "Will now execute ".$cmd);
  if ( !$mysql->query( $cmd ) ) {
     file_put_contents('php://stderr', 'MySQL "GRANT PRIVILEGES" Error: ' . $mysql->error . "\n");
     $mysql->close();
     exit(1);    
   }
  if ( !$mysql->query("FLUSH PRIVILEGES") ) {
     file_put_contents('php://stderr', 'MySQL "FLUSH PRIVILEGES" Error: ' . $mysql->error . "\n");
     $mysql->close();
     exit(1);    
   }   
  $mysql->close();
EOPHP

echo " "
echo "...DONE"
echo "----------------------------"


cd /var/www/html

##
## GENERATE LocalSettings.php if there is none
##
if [ ! -e "LocalSettings.php" ]; then
  php maintenance/install.php \
    --confpath /var/www/html \
    --dbname         "$MEDIAWIKI_DB_NAME" \
    --dbport         "$MEDIAWIKI_DB_PORT" \
    --dbserver       "$MEDIAWIKI_DB_HOST" \
    --dbtype         "$MEDIAWIKI_DB_TYPE" \
    --dbuser         "$MEDIAWIKI_DB_USER" \
    --dbpass         "$MEDIAWIKI_DB_PASSWORD" \
    --installdbuser  "$MEDIAWIKI_DB_USER" \
    --installdbpass  "$MEDIAWIKI_DB_PASSWORD" \
    --server         "$MEDIAWIKI_SITE_SERVER" \
    --scriptpath     "" \
    --lang           "$MEDIAWIKI_SITE_LANG" \
    --pass           "$MEDIAWIKI_ADMIN_PASS" \
    "$MEDIAWIKI_SITE_NAME" \
    "$MEDIAWIKI_ADMIN_USER"

# must patch in DynamicPageList3 due to issues with the path
    echo "wfLoadExtension ('DynamicPageList3');" >> LocalSettings.php        
    echo "@include('/var/www/html/custom.php');" >> LocalSettings.php   
    echo "@include('/var/www/html/myExtensions/dynamicInject.php');" >> LocalSettings.php

    # If we have a mounted share volume, move the LocalSettings.php to it
    # so it can be restored if this container needs to be reinitiated
    if [ -d "$MEDIAWIKI_SHARED" ]; then
      # Move generated LocalSettings.php to share volume
      mv LocalSettings.php "$MEDIAWIKI_SHARED/LocalSettings.php"
      ln -s "$MEDIAWIKI_SHARED/LocalSettings.php" LocalSettings.php
    fi
fi


# If a composer.lock and composer.json file exist, use them to install dependencies for MediaWiki and desired extensions, skins, etc.
if [ -e "$MEDIAWIKI_SHARED/composer.lock" -a -e "$MEDIAWIKI_SHARED/composer.json" ]; then
  curl -sS https://getcomposer.org/installer | php
  cp "$MEDIAWIKI_SHARED/composer.lock" composer.lock
  cp "$MEDIAWIKI_SHARED/composer.json" composer.json
  php composer.phar install --no-dev
fi

## Run the update.php maintenance script. If already up to date, it won't do anything, otherwise it will
## migrate the database if necessary on container startup. It also will verify the database connection is working.
if [ -e "LocalSettings.php" -a "$MEDIAWIKI_RUN_UPDATE_SCRIPT" = 'true' ]; then
  echo >&2 'info: Running maintenance/update.php';
  php maintenance/update.php --quick --conf ./LocalSettings.php
fi

## Initialize some initial pages for the Mediawiki
cd /opt/initial-contents
source /opt/initial-contents/populate.sh




