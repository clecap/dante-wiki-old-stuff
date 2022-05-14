#!/bin/zsh
#
# List all configured dantepedias
#
#
source customize-PRIVATE.sh

echo " \n"
echo "Currently configured dantepedias in this installation are: \n"

for i in $WIKIS 
do
  echo ${(r:30:)i} " at " ${MEDIAWIKI_SITE_PROTOCOL[$i]}://${MEDIAWIKI_SITE_DOMAIN[$i]}:${MEDIAWIKI_SITE_PORT[$i]}${MEDIAWIKI_SITE_PATH[$i]}
done

echo " "
echo "On MacOS: Right-click on URL to open in browser "
echo " "
