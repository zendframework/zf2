@ECHO OFF
REM Zend Framework
REM
REM runtests.bat - Launch PHPUnit for specific test group(s).
REM
REM Usage: runtests.bat [ -h <html-dir> ] [ -c <clover-xml-file> ]
REM                     [ ALL | <test-group> [ <test-group> ... ] ]
REM
REM This script makes it easier to execute PHPUnit test runs from the
REM shell, using @group tags defined in the test suite files to run
REM subsets of tests.
REM
REM To get a list of all @group tags:
REM     phpunit --list-groups AllTests.php
REM
REM LICENSE
REM
REM This source file is subject to the new BSD license that is bundled
REM with this package in the file LICENSE.txt.
REM It is also available through the world-wide-web at this URL:
REM http://framework.zend.com/license/new-bsd
REM If you did not receive a copy of the license and are unable to
REM obtain it through the world-wide-web, please send an email
REM to license@zend.com so we can send you a copy immediately.
REM
REM @category   Zend
REM @package    UnitTests
REM @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
REM @license    http://framework.zend.com/license/new-bsd     New BSD License

SET PHPUNIT=phpunit
SET PHPUNIT_OPTS=--verbose
SET PHPUNIT_GROUPS=
SET PHPUNIT_COVERAGE=
SET PHPUNIT_TEMP=

:Loop
    IF "%1"=="" GOTO Next

    REM ALL COMMAND
	IF "%1"=="ALL" GOTO All
	IF "%1"=="all" GOTO All
	IF "%1"=="MAX" GOTO All
	IF "%1"=="max" GOTO All

    REM HTML COMMAND
    IF "%1"=="-h" GOTO Html
    IF "%1"=="--html" GOTO Html

    REM CLOVER COMMAND
    IF "%1"=="-c" GOTO Clover
    IF "%1"=="--clover" GOTO Clover

	REM SERVICE GROUP
	IF "%1"=="Akismet" GOTO Service
	IF "%1"=="Amazon" GOTO Service
	IF "%1"=="Amazon_Ec2" GOTO Service
	IF "%1"=="Amazon_S3" GOTO Service
	IF "%1"=="Amazon_Sqs" GOTO Service
	IF "%1"=="Audioscrobbler" GOTO Service
	IF "%1"=="Delicious" GOTO Service
	IF "%1"=="Flickr" GOTO Service
	IF "%1"=="LiveDocx" GOTO Service
	IF "%1"=="Nirvanix" GOTO Service
	IF "%1"=="ReCaptcha" GOTO Service
	IF "%1"=="Simpy" GOTO Service
	IF "%1"=="SlideShare" GOTO Service
	IF "%1"=="StrikeIron" GOTO Service
	IF "%1"=="Technorati" GOTO Service
	IF "%1"=="Twitter" GOTO Service
	IF "%1"=="WindowsAzure" GOTO Service
	IF "%1"=="Yahoo" GOTO Service

	REM AMAZON SERVICE GROUP
	IF "%1"=="Ec2" GOTO Amazon
	IF "%1"=="S3" GOTO Amazon

	REM SEARCH GROUP
	IF "%1"=="Search" GOTO Search

	REM ZEND GROUP
	SET PHPUNIT_TEMP=%1 
    IF "%PHPUNIT_TEMP:~0,4%"=="Zend" GOTO Zend

	REM OTHER GROUPS
	IF NOT "%1"=="" GOTO Others

    SHIFT
    GOTO Loop

:All
SET PHPUNIT_GROUPS=
SHIFT
GOTO Loop 

:Html
SET PHPUNIT_COVERAGE=--coverage-html %2
SHIFT
SHIFT
GOTO Loop

:Clover
SET PHPUNIT_COVERAGE=--coverage-clover %2
SHIFT
SHIFT
GOTO Loop

:Service
SET PHPUNIT_GROUPS=%PHPUNIT_GROUPS%,Zend_Service_%1
SHIFT
GOTO Loop

:Amazon
SET PHPUNIT_GROUPS=%PHPUNIT_GROUPS%,Zend_Service_Amazon_%1
SHIFT
GOTO Loop

:Search
SET PHPUNIT_GROUPS=%PHPUNIT_GROUPS%,Zend_Search_Lucene
SHIFT
GOTO Loop

:Zend
SET PHPUNIT_GROUPS=%PHPUNIT_GROUPS%,%1
SET PHPUNIT_TEMP=
SHIFT
GOTO Loop

:Others
SET PHPUNIT_GROUPS=%PHPUNIT_GROUPS%,Zend_%1
SHIFT
GOTO Loop

:Next
SET PHPUNIT_TEMP=
IF NOT "%PHPUNIT_GROUPS:~0,1%" == "," GOTO Next2
SET PHPUNIT_GROUPS=%PHPUNIT_GROUPS:~1%
SET PHPUNIT_GROUPS=--group %PHPUNIT_GROUPS%

:Next2
@ECHO ON
%PHPUNIT% %PHPUNIT_OPTS% %PHPUNIT_COVERAGE% %PHPUNIT_GROUPS%
