#!/bin/bash

docker swarm init
docker stack deploy -c docker-stack.yml wordpress
