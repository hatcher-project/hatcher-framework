#!/bin/bash

set -e

SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")


echo -e "\e[34m"
echo "======================"
echo -e "= \e[1m\e[33mRunning unit tests\e[0m\e[34m ="
echo -e "======================\e[39m"

phpunit -c "$SCRIPTDIR/../../phpunit.dist.xml" --coverage-clover "$SCRIPTDIR/../../build/logs/clover.xml"


echo -e "\e[34m"
echo "=========================="
echo -e "= \e[1m\e[33mRunning specifications\e[0m\e[34m ="
echo -e "==========================\e[39m"


php "$SCRIPTDIR/../../vendor/bin/pho" -b  "$SCRIPTDIR/../../test/bootstrap-tests.php"   "$SCRIPTDIR/../../test/suites/spec/"


echo -e "\e[34m"
echo "================================="
echo -e "= \e[1m\e[33mChecking code style standards\e[0m\e[34m ="
echo -e "=================================\e[39m"

$SCRIPTDIR/phpcs.bash $1

echo "OK"

if [ "$PROCESS_CODECLIMATE" = true ] && [ "${TRAVIS_PULL_REQUEST}" = "false" ] && [ "${TRAVIS_BRANCH}" = "master" ]
then

    composer require codeclimate/php-test-reporter:dev-master

    echo -e "\e[34m"
    echo "============================"
    echo -e "= \e[1m\e[33mRepporting code coverage\e[0m\e[34m ="
    echo -e "============================\e[39m"

    ./vendor/bin/test-reporter
fi


echo
echo -e "\e[1m\e[42mAll test passed!\e[0m"
echo
