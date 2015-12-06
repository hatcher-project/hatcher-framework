#!/bin/bash

set -e

SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")


echo ""
echo "======================"
echo "= Running test suite ="
echo "======================"

phpunit -c "$SCRIPTDIR/../../phpunit.dist.xml" --coverage-clover "$SCRIPTDIR/../../build/logs/clover.xml"


echo ""
echo "================================="
echo "= Checking code style standards ="
echo "================================="

$SCRIPTDIR/phpcs.bash $1

echo "OK"


if [ "$PROCESS_CODECLIMATE" = true ] && [ "${TRAVIS_PULL_REQUEST}" = "false" ] && [ "${TRAVIS_BRANCH}" = "master" ]
then

    echo ""
    echo "============================"
    echo "= Repporting code coverage ="
    echo "============================"

    ./vendor/bin/test-reporter
fi

echo ""
echo "==================================="
echo "= Processing copy/paste detection ="
echo "==================================="

php "$SCRIPTDIR/../../vendor/bin/phpcpd" --verbose --no-interaction "$SCRIPTDIR/../../src/"
