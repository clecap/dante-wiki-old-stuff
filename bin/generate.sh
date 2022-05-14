#!/bin/zsh
#
# Downloads the mysql docker image and generates the dantewiki docker image

docker pull mysql:8.0.27
docker pull phpmyadmin:5.0
docker build -t dantewiki dantewiki-docker-context