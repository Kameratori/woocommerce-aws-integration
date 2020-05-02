#!/bin/bash

# gather exit codes
exitCodeArray=()
onFailure() { exitCodeArray+=( "$?" ); }
trap onFailure ERR

# tests
docker-compose exec -T wordpress codecept run unit
docker-compose exec -T wordpress codecept run wpunit
docker-compose exec -T wordpress codecept run acceptance

# check if any test failed
add () { local IFS='+'; printf "%s" "$(( $* ))"; }
if (( $(add "${exitCodeArray[@]}") )); then
	# test fail
  exit 1
else
	# test success
	exit 0
fi
