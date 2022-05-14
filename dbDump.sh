#!/bin/zsh

if [ $# -eq 0 ]; then
    echo "Usage:    ./dbDump.sh  Name-of-Dantewiki  "
    echo "Example:  ./dbDump.sh  mathewiki "
    echo "Purpose:  Write a full database dump to directory dumps, including time stamp and name into the filename"
    exit 1
fi

NAME=$1

## customize-PRIVATE.sh contains all the settings of the user for all her environment parameters
source customize-PRIVATE.sh

## adjust sets some defaults and picks the final parameters from the provided name and the data in customize-PRIVATE.sh
source adjust.sh

# prevent overwriting an existing dump file
set -o noclobber

rm -f dump-errors

# CAVE: below, do not use -t in docker exec to prevent warnings from mysqldump to show up in the sql file
#       See https://github.com/docker-library/mysql/issues/132  for more details 
docker exec -i my-mysql-${NAME} mysqldump -u ${MEDIAWIKI_DB_USER} --password=${MEDIAWIKI_DB_PASSWORD} --all-databases > dumps/dump-${NAME}-$(date '+%d-%m-%Y-at-%T').sql 2>dump-errors

cat dump-errors

