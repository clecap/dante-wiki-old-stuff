#!/bin/zsh

if [ $# -eq 0 ]; then
    echo "Usage:    ./check.sh  Name-of-Dantewiki  "
    echo "Example:  ./check.sh  public "
    echo "Purpose:  Check the settings of a named dantewiki by listing its parameters"
    exit 1
fi

source customize-PRIVATE.sh
source adjust.sh $1


echo "  \n"
echo "The Dantepeida with the name ==**  " $1 "  **== has the following parameter settings:"
echo " \n" 

## Site server
if (( ${+MEDIAWIKI_SITE_SERVER} )); then 
  echo "Site server:               " ${MEDIAWIKI_SITE_SERVER}; 
else 
  echo "Site server is ** MISSING **" ;
fi

## Language
if (( ${+MEDIAWIKI_SITE_LANG} )); then 
  echo "Language:                  " ${MEDIAWIKI_SITE_LANG}; 
else 
  echo "Language is MISSING" ;
fi

## admin Password
if (( ${+MEDIAWIKI_ADMIN_PASS} )); then 
  echo "Mediawiki admin password:  " ${MEDIAWIKI_ADMIN_PASS}; 
else 
  echo "Mediawiki admin password is MISSING" ;
fi

echo " "

## mysql root password
if (( ${+MYSQL_ROOT_PASSWORD} )); then 
  echo "Mysql root password:       " ${MYSQL_ROOT_PASSWORD}; 
else 
  echo "Mysql root password is MISSING" ;
fi



if (( ${+MEDIAWIKI_ADMIN_USER} )); then 
  echo "Admin User Name:           " ${MEDIAWIKI_ADMIN_USER}; 
else 
  echo "Admin User Name: MISSING" ;
fi

echo "\n"

