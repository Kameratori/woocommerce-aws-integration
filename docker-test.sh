#!/bin/bash

set -e

# Run codecept
docker-compose exec -T wordpress codecept run