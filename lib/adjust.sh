#!/bin/zsh
#
# Adjust is not to be called by the end user. 
# It is provided with the name of the dantepedia and picks the correct parameters from customize-PRIVATE.sh
#

# load the database of user settings
source conf/customize-PRIVATE.sh

## PICK values from the user settings, depending on the names of the site
MEDIAWIKI_SITE_PROTOCOL=${MEDIAWIKI_SITE_PROTOCOL[$1]}
MEDIAWIKI_SITE_DOMAIN=${MEDIAWIKI_SITE_DOMAIN[$1]}
MEDIAWIKI_SITE_PORT=${MEDIAWIKI_SITE_PORT[$1]}
MEDIAWIKI_SITE_PATH=${MEDIAWIKI_SITE_PATH[$1]}

MEDIAWIKI_SITE_LANG=${MEDIAWIKI_SITE_LANG[$1]}
MEDIAWIKI_ADMIN_PASS=${MEDIAWIKI_ADMIN_PASS[$1]}

MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD[$1]}

## BUILD some dependent values
MEDIAWIKI_SITE_SERVER=${MEDIAWIKI_SITE_PROTOCOL}://${MEDIAWIKI_SITE_DOMAIN}:${MEDIAWIKI_SITE_PORT}${MEDIAWIKI_SITE_PATH}
MEDIAWIKI_SITE_NAME=$1


## NOW we set some default values
MEDIAWIKI_ADMIN_USER="admin"
MEDIAWIKI_DB_HOST="my-mysql" 
MEDIAWIKI_DB_TYPE="mysql"      
MEDIAWIKI_DB_PORT=3306 


MEDIAWIKI_RUN_UPDATE_SCRIPT=true
MEDIAWIKI_SLEEP=0



