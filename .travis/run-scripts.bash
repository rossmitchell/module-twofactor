#!/usr/bin/env bash



if [[ ${GENERATE_COVERAGE} == "1" ]]
then
    cd ${TRAVIS_BUILD_DIR}/magento2/dev/tests/integration
    ../../../vendor/bin/phpunit -c $PWD/phpunit.xml --coverage-text --coverage-php=${TRAVIS_BUILD_DIR}/build/cov/clover-integration.cov
    cd ${TRAVIS_BUILD_DIR}/magento2/dev/tests/unit
    ../../../vendor/bin/phpunit -c $PWD/phpunit.xml --coverage-text --coverage-php=${TRAVIS_BUILD_DIR}/build/cov/clover-unit.cov
    php ${TRAVIS_BUILD_DIR}/magento2/vendor/bin/phpcov.php merge --clover ${TRAVIS_BUILD_DIR}/build/logs/clover.xml ${TRAVIS_BUILD_DIR}/build/cov
else
    cd ${TRAVIS_BUILD_DIR}/magento2/dev/tests/${TEST_TYPE}
    ../../../vendor/bin/phpunit -c $PWD/phpunit.xml
fi
