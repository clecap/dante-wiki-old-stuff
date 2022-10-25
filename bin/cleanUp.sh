#!/bin/zsh 
#
#
#

if [ $# -eq 0 ]; then
    echo "Usage:    ./cleanUp.sh  Name-of-Dantewiki "
    echo "Example:  ./cleanUp.sh  public            "
    echo "Purpose:  Cleans up archived files and old revisions"
    exit 1
fi

NAME=$1

docker exec -it my-dante-${NAME} php /var/www/html/maintenance/deleteArchivedFiles.php --delete --force

docker exec -it my-dante-${NAME} php /var/www/html/maintenance/deleteOldRevisions.php --delete 


