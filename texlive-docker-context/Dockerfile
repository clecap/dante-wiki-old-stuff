FROM debian:11.1

ENV LANG=C.UTF-8 \
    LC_ALL=C.UTF-8 \ 
    # ConTeXt cache can be created on runtime and does not need to
    # increase image size
    TEXLIVE_INSTALL_NO_CONTEXT_CACHE=1 \ 
    # As we will not install regular documentation why would we want to
    # install perl docs…
    NOPERLDOC=1 \ 
    # The base mirror is one of the mirrors of TUG's historic archive
    TLHISTMIRRORURL=rsync://texlive.info/historic/systems/texlive \ 
    # To get the latest packages available we always use the root mirror
    TLMIRRORURL=http://dante.ctan.org/tex-archive/systems/texlive/tlnet


RUN apt-get update && \ 
  # basic utilities for TeX Live installation
  apt-get install -y wget rsync unzip git gpg tar xorriso \ 
  # miscellaneous dependencies for TeX Live tools
  make fontconfig perl default-jre libgetopt-long-descriptive-perl \
  libdigest-perl-md5-perl libncurses5 libncurses6 \
  # for latexindent (see #13)
  libunicode-linebreak-perl libfile-homedir-perl libyaml-tiny-perl \
  # for eps conversion (see #14)
  ghostscript \
  # for l3build CTAN upload
  curl \
  # for syntax highlighting
  python3 python3-pygments && \ 
  rm -rf /var/lib/apt/lists/* && \
  rm -rf /var/cache/apt/ && \ 
  # bad fix for python handling
  ln -s /usr/bin/python3 /usr/bin/python
  
  # whether to install documentation and/or source files
# this has to be yes or no
ARG DOCFILES=no
ARG SRCFILES=no

RUN apt-get update && \
  # Mark all texlive packages as installed. This enables installing latex-related packges in child images.
  # Inspired by https://tex.stackexchange.com/a/95373/9075.
  apt install -qy equivs --no-install-recommends freeglut3 && \
  mkdir -p /tmp/tl-equivs && \
  # we need to change into tl-equis to get it working
  cd /tmp/tl-equivs && \
  wget -q -O texlive-local http://www.tug.org/texlive/files/debian-equivs-2021-ex.txt && \
  sed -i "s/2021/9999/" texlive-local && \
  equivs-build texlive-local && \
  dpkg -i texlive-local_9999.99999999-1_all.deb && \
  apt install -qyf && \
  # reverse the cd command from above and cleanup
  cd .. && \
  rm -rf /tmp/tl-equivs && \
  # save some space
  apt remove -y --purge equivs && \
  apt-get autoremove -qy --purge && \
  rm -rf /var/lib/apt/lists/* && \
  apt-get clean && \
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

RUN \
  # test the installation
  latex --version && printf '\n' && \
  biber --version && printf '\n' && \
  xindy --version && printf '\n' && \
  arara --version && printf '\n' && \
  python --version && printf '\n' && \
  pygmentize -V && printf '\n' && \
  if [ "$DOCFILES" = "yes" ]; then texdoc -lI geometry; fi && \
  if [ "$SRCFILES" = "yes" ]; then kpsewhich latexbug.dtx; fi

