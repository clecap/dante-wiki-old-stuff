# Build the dante-mediawiki image
#
FROM debian:11.1

## Install stuff
##
RUN set -eux; \  
  apt-get update; \
  # install PHP requirements
  apt-get install -y --no-install-recommends php7.4 php7.4-mysql php7.4-gd php7.4-curl php7.4-mbstring php7.4-xml  php7.4-intl; \  
  # install apcu and igbinary as suggested by mediawiki optimization; install fpm for faster interface to apache;   
  apt-get install -y --no-install-recommends php7.4-apcu php7.4-igbinary php7.4-fpm; \    
  # install additional tools
  # python3: for syntax highlighting; ca-certificates: for wget not to fail on https; vim: to have an editor should we need some checking; netcat for initialize.sh script
  apt-get install -y --no-install-recommends  git apache2 libapache2-mod-php7.4 wget ca-certificates python3 vim netcat; \  
  # requirements for pdf handler and thumbnails, see https://www.mediawiki.org/wiki/Extension:PdfHandler#Pre-requisites  and  https://www.mediawiki.org/wiki/Topic:V8h8s9f2xtu5wmsx
  apt-get install -y --no-install-recommends imagemagick ghostscript xpdf-utils;\  
  # needed for handling svg 
  apt-get install -y --no-install-recommends librsvg2-bin;\   
  # enable apache modules; disable those in conflict with php (see https://stackoverflow.com/questions/42506956/sudo-a2enmod-php5-6-php-v-still-shows-php-7-01-conflict )
  a2enmod rewrite proxy proxy_http; \      
  a2dismod mpm_prefork mpm_worker mpm_event; \
  a2enmod php7.4; \ 
  a2enmod proxy_fcgi setenvif; \
  a2enconf php7.4-fpm; \
  # Remove the default Debian index page.
  rm /var/www/html/index.html; \
  # Obtain Mediaiki sources
  cd /var/www/html ;\
  wget https://releases.wikimedia.org/mediawiki/1.37/mediawiki-1.37.0.tar.gz; \
  tar -xvzf mediawiki-1.37.0.tar.gz; \
  rm mediawiki-1.37.0.tar.gz; \
  cd mediawiki-1.37.0; \
  mv * .. ; \
  cd .. ;\
  rmdir mediawiki-1.37.0; \
  cd /var/www/html ; \
  # Install Mediawiki extensions (ADD LATER or rather do it via a COPY below)
#  git clone "https://gerrit.wikimedia.org/r/mediawiki/skins/MinervaNeue" skins/MinervaNeue; \
#  git clone "https://gerrit.wikimedia.org/r/mediawiki/extensions/MobileFrontend" extensions/MobileFrontend; \
# curl --remote-name https://extdist.wmflabs.org/dist/extensions/CategoryTree-REL1_32-5866bb9.tar.gz
# tar -xzf CategoryTree-REL1_32-5866bb9.tar.gz -C /var/www/html/extensions
  # ENSURE directory. permissions and ownership
  mkdir -p /var/www/images;   \
  chmod 755 /var/www/images;  \
  chown -R www-data:www-data /var/www/html; \ 
  # CLEAN UP: Keep the docker image as small as possible
  apt-get autoremove --purge -y; apt-get autoclean -y; apt-get clean -y; \
  rm -rf /var/lib/apt/lists/* ; \
  rm -rf /tmp/* /var/tmp/* ; \
  rm -rf /var/cache/apt/archives/*

# Copy in PHP configuration file local version for our version of PHP; we need this for apache2 and for cli (for the config script of LocalSettings.php)
COPY mediawiki-php.ini /etc/php/7.4/apache2/conf.d/mediawiki-php.ini
COPY mediawiki-php.ini /etc/php/7.4/cli/conf.d/mediawiki-php.ini

# Copy in some extensions (especially DynamicPageList3, which proved a bit tricky regarding some aspects)
COPY DynamicPageList3 /var/www/html/extensions/DynamicPageList3

# Copy in Apache configuration files
COPY mediawiki-apache2.conf /etc/apache2/
COPY favicon.ico /var/www/html/favicon.ico
RUN echo "Include /etc/apache2/mediawiki-apache2.conf" >> /etc/apache2/apache2.conf

COPY initialize.sh /initialize.sh
RUN chmod 755 /initialize.sh

RUN mkdir /opt/initial-contents
COPY initial-contents/* /opt/initial-contents

# Expose ports of the container to the outside world
EXPOSE 80 443

# ENTRYPOINT ["/docker-entry.sh"]

CMD ["apachectl", "-e", "info", "-D", "FOREGROUND"]