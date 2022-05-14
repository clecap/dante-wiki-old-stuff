
## Design Decisions and Lessons Learnt

### Debug support

Should the container not come up reasonably, then uncomment the loop in docker-entry.sh to keep the container running.
Then you can open a terminal to the container, remove the loop and manually check the execution of the install shell.

### 1. php.inclusion

The exact placement of the php.ini file is a bit tricky. The settings can be made different for command line and
apache2 use, so checking the proper settings for apache2 via CLI will fail.


### 2. Optimization

The docker image uses the following optimizations:
* Using OPcache according to https://www.mediawiki.org/wiki/Manual:Performance_tuning#Bytecode_caching
* Using APCU cache according to https://www.mediawiki.org/wiki/Manual:Performance_tuning#Object_caching
* Using Apache PHP-FPM according to https://www.mediawiki.org/wiki/Manual:Performance_tuning#Web_server

### 3. Versioning

We use fixed versions and not `latest` or similar semantic tags, as having a stable and tested version number
improves the reproducibility of the build process.

### 4. Docker Know How

* It is good operating practice, to keep the docker context free from credentials, as the context gets sent 
  to the docker server, which might be remote. See: https://codefresh.io/docker-tutorial/not-ignore-dockerignore-2/


## DynamicPageList3

* Looks like most links on this in Mediawiki are old.
* https://github.com/Universal-Omega/DynamicPageList3/releases/tag/3.3.7



## References

Sources I consulted or used when working on this:
* https://github.com/Sundin/mediawiki-docker via MIT License
* https://github.com/wikimedia/mediawiki-docker
