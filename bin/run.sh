#!/bin/zsh
#
# Start two containers for Mysql and Mediawiki
#

if [ $# -eq 0 ]; then
    echo "Usage:    ./run.sh  Name-of-Dantewiki "
    echo "Example:  ./run.sh  public            "
    echo "Purpose:  starts a fresh dantewiki instance"
    exit 1
fi


NAME=$1

## customize-PRIVATE.sh contains all the settings of the user for all her environment parameters
source ../conf/customize-PRIVATE.sh

## adjust sets some defaults and picks the final parameters from the provided name and the data in customize-PRIVATE.sh
source ../lib/adjust.sh


# -d   run as daemon in background
# -e   set environment variable
#
#

## clean up existing old stuff
echo "CLEANING UP EXISTING CONTAINERS OF THE SAME NAME...\n"

docker container stop my-mysql-${NAME}
docker container rm   my-mysql-${NAME} 

docker container stop my-dante-${NAME}
docker container rm   my-dante-${NAME} 

docker network   rm   mynetwork-${NAME}

echo "...DONE\n\n"

echo "CREATING NETWORK mynetwork-${NAME}...\n"
docker network create mynetwork-${NAME}
echo "...DONE\n\n"

echo "STARTING UP DATABASE my-mysql-${NAME}...${MYSQL_ROOT_PASSWORD}\n"
docker run -d --name my-mysql-${NAME} --network mynetwork-${NAME} -e MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD}"   mysql:8.0.27 mysqld --default-authentication-plugin=mysql_native_password
echo "...DONE\n\n"

# 3306 is the standard port of mysql


# run the dantewiki image to produce container my-dante, providing environment for initialization 
# need -t to ensure we keep it running 
echo "STARTING UP DANTEWIKI my-dante-${NAME}...\n"
docker run -td --name my-dante-${NAME}  \
  -v /Users/cap/DOCKER/dantewiki/dantewiki-volume:/var/www/html/myExtensions     \
  --network mynetwork-${NAME}                               \
  -p ${MEDIAWIKI_SITE_PORT}:80                                                 \
  -e MEDIAWIKI_SITE_SERVER="${MEDIAWIKI_SITE_SERVER}"    \
  -e MEDIAWIKI_SITE_NAME="${NAME}"                 \
  -e MEDIAWIKI_SITE_LANG="${MEDIAWIKI_SITE_LANG}"                         \
  -e MEDIAWIKI_ADMIN_USER="${MEDIAWIKI_ADMIN_USER}"                     \
  -e MEDIAWIKI_ADMIN_PASS="${MEDIAWIKI_ADMIN_PASS}"              \
  -e MEDIAWIKI_RUN_UPDATE_SCRIPT=true               \
  -e MEDIAWIKI_SLEEP=0                              \
  -e MEDIAWIKI_DB_HOST=my-mysql-${NAME}                     \
  -e MEDIAWIKI_DB_USER=root                         \
  -e MEDIAWIKI_DB_PASSWORD="${MYSQL_ROOT_PASSWORD}"   \
  -e MEDIAWIKI_DB_TYPE="${MEDIAWIKI_DB_TYPE}"                 \
  -e MEDIAWIKI_DB_PORT="${MEDIAWIKI_DB_PORT}"                         \
dantewiki
echo "...DONE\n\n"

echo "INITIALIZING DANTEWIKI my-dante-${NAME}...\n"
docker exec -it my-dante-${NAME} /initialize.sh
# docker exec -it <container_id_or_name> echo "I'm inside the container!"

## while editing stuff, remove the opcache (or else edits will be reflected very slowly only)
## when no longer editing stuff, COMMENT this line
docker exec -it my-dante-${NAME} /bin/rm /etc/php/7.4/apache2/conf.d/mediawiki-php.ini

echo "\n...DONE\n\n"




















