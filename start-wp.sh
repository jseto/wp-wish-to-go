#!/bin/bash

docker swarm init
docker stack deploy -c docker-stack${1}.yml wordpress${1}
