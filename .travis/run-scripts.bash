#!/usr/bin/env bash

cd ${TRAVIS_BUILD_DIR}/magento2/dev/tests/integration

if [[ ${GENERATE_COVERAGE} == "1" ]]
then
    ../../../vendor/bin/phpunit -c $PWD/phpunit.xml --coverage-text --coverage-clover=${TRAVIS_BUILD_DIR}/coverage.clover
else
    ../../../vendor/bin/phpunit -c $PWD/phpunit.xml
fi
