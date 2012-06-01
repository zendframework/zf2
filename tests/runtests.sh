:
# Zend Framework
#
# testgroup.sh - Launch PHPUnit for specific test group(s).
#
# Usage: testgroup.sh [ -h <html-dir> ] [ -c <clover-xml-file> ] [ -g ]
#     [ ALL | <test-group> [ <test-group> ... ] ]
#
# This script makes it easier to execute PHPUnit test runs from the
# shell, using @group tags defined in the test suite files to run
# subsets of tests.
#
# To get a list of all @group tags:
#     phpunit --list-groups AllTests.php
#
# LICENSE
#
# This source file is subject to the new BSD license that is bundled
# with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# http://framework.zend.com/license/new-bsd
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@zend.com so we can send you a copy immediately.
#
# @category   Zend
# @package    UnitTests
# @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License

: ${BASEDIR:=$(dirname $0)}
: ${PHPUNIT:="phpunit"}
: ${PHPUNIT_CONF:="$BASEDIR/phpunit.xml.dist"}
if [ -e "$BASEDIR/phpunit.xml" ]
then
  PHPUNIT_CONF="$BASEDIR/phpunit.xml"
fi
: ${PHPUNIT_OPTS:="-c $PHPUNIT_CONF"}
: ${PHPUNIT_GROUPS:=""}
: ${RUN_AS_GROUPS:=false}
: ${RESULT:=0}

while [ -n "$1" ] ; do
  case "$1" in
    -h|--html)
      PHPUNIT_COVERAGE="--coverage-html $2"
      shift 2 ;;

    -c|--clover)
      PHPUNIT_COVERAGE="--coverage-clover $2"
      shift 2 ;;

    -g|--groups)
      RUN_AS_GROUPS="true"
      shift 1 ;;

    ALL|all|MAX|max)
      if ${RUN_AS_GROUPS:=true}
      then
        PHPUNIT_GROUPS=""
      else
        # Add every component present in /library/
        for i in $(ls -d ${BASEDIR}/../library/Zend/*/)
        do
          PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS "}${BASEDIR}/Zend/$(basename $i)/"
        done

        # Add individual test files present in /tests/Zend
        for i in $(ls ${BASEDIR}/Zend/*Test.php)
        do
          PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS "}${BASEDIR}/Zend/$(basename $i)"
        done

        # Exclude files/directories excluded in phpunit.xml.dist or phpunit.xml (if available)
        for i in $(grep "<exclude>.*</exclude>" $PHPUNIT_CONF | sed 's#.*<exclude>./Zend/\(.*\)</exclude>.*#\1#')
        do
          PHPUNIT_GROUPS=${PHPUNIT_GROUPS//${BASEDIR}\/Zend\/${i}/}
        done
      fi
      break ;;

    Akismet|Amazon|Amazon_Ec2|Amazon_S3|Amazon_Sqs|Audioscrobbler|Delicious|Flickr|GoGrid|LiveDocx|Nirvanix|Rackspace|ReCaptcha|Simpy|SlideShare|StrikeIron|Technorati|Twitter|WindowsAzure|Yahoo)
      PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_Service_$1"
      shift ;;

    Ec2|S3)
      PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_Service_Amazon_$1"
      shift ;;

    Search)
      PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_Search_Lucene"
      shift ;;

    Zend*)
      PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}$1"
      shift ;;

    *)
      PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_$1"
      shift ;;
  esac
done

if ${RUN_AS_GROUPS:=true}
then
  ${PHPUNIT} ${PHPUNIT_OPTS} ${PHPUNIT_COVERAGE} ${PHPUNIT_DB} \
    ${PHPUNIT_GROUPS:+--group $PHPUNIT_GROUPS}
  RESULT=$?
else
  # Replace commas with spaces and underscores with slashes
  PHPUNIT_GROUPS=${PHPUNIT_GROUPS//,/ }
  PHPUNIT_GROUPS=${PHPUNIT_GROUPS//_/\/}
  for i in ${PHPUNIT_GROUPS}
  do
    echo "$i:"
    ${PHPUNIT} ${PHPUNIT_OPTS} ${PHPUNIT_COVERAGE} ${PHPUNIT_DB} $i
    RESULT=$(($RESULT || $?))
  done
fi

exit $RESULT
