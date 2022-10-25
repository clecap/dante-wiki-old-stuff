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
  # we need php-pear as we need pecl for installing the php datastructre extension - which we need in Parsifal for cleaning up the cache
  # we need php7.4-dev for phpize which we need for pecl install
  apt-get install -y php7.4-dev php-pear;\
  # honor an explicit request by pecl:
  pecl channel-update pecl.php.net;\
  pecl install ds;    \
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



# Copy in PHP configuration file local version for our version of PHP; 
# we need this for apache2 and for cli (cli for the configuration script of LocalSettings.php)
COPY mediawiki-php.ini /etc/php/7.4/apache2/conf.d/mediawiki-php.ini
COPY mediawiki-php.ini /etc/php/7.4/cli/conf.d/mediawiki-php.ini

# Copy in some extensions (especially DynamicPageList3, which proved a bit tricky regarding some aspects)
COPY DynamicPageList3 /var/www/html/extensions/DynamicPageList3

# Copy in Apache configuration files into apache2 directory and patch apache2.conf to have it loaded
COPY mediawiki-apache2.conf /etc/apache2/
RUN echo "Include /etc/apache2/mediawiki-apache2.conf" >> /etc/apache2/apache2.conf

COPY favicon.ico /var/www/html/favicon.ico

# copy in an initialization shell command; will be run by bin/run.sh
COPY initialize.sh /initialize.sh
RUN chmod 755 /initialize.sh

# copy in some initial content pages for the wiki; will be installed by initialize.sh which will be run by bin/run.sh
RUN mkdir /opt/initial-contents
COPY initial-contents/* /opt/initial-contents

# Expose ports of the container to the outside world
EXPOSE 80 443





##
## Tex Live portion from https://gitlab.com/islandoftex/images/texlive/-/blob/master/Dockerfile
##

ENV LANG=C.UTF-8 \
  LC_ALL=C.UTF-8 \ 
  # ConTeXt cache can be created on runtime and does not need to increase image size
  TEXLIVE_INSTALL_NO_CONTEXT_CACHE=1 \ 
  # As we will not install regular documentation why would we want to install perl docs…
  NOPERLDOC=1 \ 
  # The base mirror is one of the mirrors of TUG's historic archive
  TLHISTMIRRORURL=rsync://texlive.info/historic/systems/texlive \ 
  # To get the latest packages available we always use the root mirror
  TLMIRRORURL=http://dante.ctan.org/tex-archive/systems/texlive/tlnet


RUN apt-get update && \ 
  # basic utilities for TeX Live installation
  apt-get install -y wget rsync unzip git gpg tar xorriso \ 
  make fontconfig perl default-jre libgetopt-long-descriptive-perl \
  libdigest-perl-md5-perl libncurses5 libncurses6 \
  libunicode-linebreak-perl libfile-homedir-perl libyaml-tiny-perl \
  ghostscript curl python3 python3-pygments && \ 
  rm -rf /var/lib/apt/lists/*     && \
  rm -rf /var/cache/apt/          && \ 
  # bad fix for python handling
  ln -s /usr/bin/python3 /usr/bin/python
  
# do not install documentation files and do not install source files of TeX
ARG DOCFILES=no
ARG SRCFILES=no

RUN apt-get update && \
  # Mark all texlive packages as installed. This enables installing latex-related packages in child images.
  # Inspired by https://tex.stackexchange.com/a/95373/9075.
  apt install -qy equivs --no-install-recommends freeglut3 && \
  mkdir -p /tmp/tl-equivs && \
  # we need to change into tl-equis to get it working
  cd /tmp/tl-equivs && \
  wget -q -O texlive-local http://www.tug.org/texlive/files/debian-equivs-2022-ex.txt && \
  sed -i "s/2022/9999/" texlive-local && \
  equivs-build texlive-local && \
  dpkg -i texlive-local_9999.99999999-1_all.deb && \
  apt install -qyf && \
  # reverse the cd command from above and cleanup
  cd .. && \
  rm -rf /tmp/tl-equivs && \
  # save some space
  apt remove -y --purge equivs    && \
  apt-get autoremove -qy --purge  && \
  rm -rf /var/lib/apt/lists/*     && \
  apt-get clean                   && \
  rm -rf /var/cache/apt/

RUN echo "Building with documentation: $DOCFILES" && \
  echo "Building with sources: $SRCFILES" && \
  # verify vanilla TeX Live installer
  wget "$TLMIRRORURL/install-tl-unx.tar.gz" && \
  wget "$TLMIRRORURL/install-tl-unx.tar.gz.sha512" && \
  wget "$TLMIRRORURL/install-tl-unx.tar.gz.sha512.asc" && \
  wget https://tug.org/texlive/files/texlive.asc && \
  gpg --import texlive.asc && \
  gpg --verify install-tl-unx.tar.gz.sha512.asc install-tl-unx.tar.gz.sha512 && \
  sha512sum -c install-tl-unx.tar.gz.sha512 && \
  rm install-tl-unx.tar.gz.sha512* && \
  rm texlive.asc && \
  rm -rf /root/.gnupg && \
  tar xzf install-tl-unx.tar.gz && \
  rm install-tl-unx.tar.gz && \
  # actually install TeX Live
  cd install-tl* && \
  # choose complete installation
  echo "selected_scheme scheme-full" > install.profile && \
  # … but disable documentation and source files when asked to stay slim
  if [ "$DOCFILES" = "no" ]; then echo "tlpdbopt_install_docfiles 0" >> install.profile && \
    echo "BUILD: Disabling documentation files"; fi && \
  if [ "$SRCFILES" = "no" ]; then echo "tlpdbopt_install_srcfiles 0" >> install.profile && \
    echo "BUILD: Disabling source files"; fi && \
  echo "tlpdbopt_autobackup 0" >> install.profile && \
  # furthermore we want our symlinks in the system binary folder to avoid
  # fiddling around with the PATH
  echo "tlpdbopt_sys_bin /usr/bin" >> install.profile && \
  ./install-tl -profile install.profile && \
  cd .. && rm -rf install-tl* && \
  # add all relevant binaries to the PATH
  $(find /usr/local/texlive -name tlmgr) path add


## mutool 
RUN  mkdir /opt/mutools
COPY mupdf-1.20.3-source /opt/mutools

RUN apt-get update && \
  apt-get install -y gperf build-essential libgl1-mesa-dev  libglu1.mesa-dev  libx11-dev  libxi-dev libxrandr-dev  && \
  cd /opt/mutools  && \
  make && \
  make install && \
  make clean && \
  apt-get install -y nodejs npm && \
  # clean up 
  apt-get purge -y build-essential && \
  apt-get autoremove -qy --purge && \
  rm -rf /var/lib/apt/lists/* && \
  apt-get clean           && \
  rm -rf /var/cache/apt/  && \
  rm -rf /opt/mutools



















CMD ["apachectl", "-e", "info", "-D", "FOREGROUND"]