#!/bin/zsh
#
# Additionally runs a php-myadmin container on the database
#

if [ $# -eq 0 ]; then
    echo "Usage:    ./runAdmin.sh  Name-of-Dantewiki "
    echo "Example:  ./runAdmin.sh  public            "
    echo "Purpose:  Adds a php-myadmin container to the running mysql container of a Dantewiki"
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

docker run --name my-phpmyadmin-${NAME} --network mynetwork-${NAME}  -d -e PMA_HOST=my-mysql-private  -p 9090:80 phpmyadmin:5.0

