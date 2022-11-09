# Architecture

## Structure
Dantewiki runs on two containers:
* A generic mysql container.
* A generic Debian container onto which we install Mediawiki with a generic content.

## Persistence 
Dantewiki stores information in four places: 
* articles in the database 
* uploaded files in the filesystem at /var/www/html/images
* system configuration in LocalSettings.php 
* custom extensions in files inside of `/var/www/html/`, most likely in subdirectory `extension`
