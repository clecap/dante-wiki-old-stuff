#!/bin/zsh 
docker exec -it my-dante php /var/www/html/maintenance/deleteArchivedFiles.php --delete --force

