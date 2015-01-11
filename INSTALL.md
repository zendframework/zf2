# INSTALLATION

Although Zend Framework requires no special installation steps, 
we highly recommend using [Composer][composer].
However, you can simply download the framework, extract it to the folder you 
would like to keep it in, and add the `library` directory to your PHP 
`include_path`.

Please refer to the [installation instructions][installation] in the [manual]
 for more details.

## DEVELOPMENT VERSIONS

If you would like to preview enhancements or bug fixes that have not yet
been released, you can obtain the current development version of Zend
Framework using one of the following methods:

 -  Using a Git client. Zend Framework is open source software, and the
    Git repository used for its development is publicly available.
    Consider using Git to get Zend Framework if you already use Git for
    your application development, want to contribute back to the
    framework, or need to upgrade your framework version very often.

 -  Checking out a working copy is necessary if you would like to directly
    contribute to Zend Framework; a working copy can be updated any time
    using git pull.

To clone the git repository, use the following URL:

git://git.zendframework.com/zf.git

For more information about Git, please see the official website:

http://www.git-scm.org

## CONFIGURING THE INCLUDE PATH

Once you have a copy of Zend Framework available, your application will
need to access the framework classes.

If you're using [Composer][composer], you just need to add `require 
"vendor/autoload.php"` at the very beginning of your application main entry 
point (i.e. `index.php`).
Please not that this has already been done for you if you're using the 
default [ZendSkeletonApplication][ZendSkeletonApplication].

However, if you're not using [Composer][composer], there are several other 
ways to achieve this: one is to set your PHP `include_path` so that it 
contains the path to the Zend Framework classes under the `/library` 
directory in this distribution. You can find out more about the PHP 
`include_path` configuration directive here:

http://www.php.net/manual/en/ini.core.php#ini.include-path

Instructions on how to change PHP configuration directives can be found
here:

http://www.php.net/manual/en/configuration.changes.php

## GETTING STARTED

A great place to get up-to-speed quickly is the Zend Framework
QuickStart:

http://framework.zend.com/manual/2.3/en/user-guide/overview.html

The QuickStart covers some of the most commonly used components of ZF.
Since Zend Framework is designed with a use-at-will architecture and
components are loosely coupled, you can select and use only those
components that are needed for your project.

[manual]: http://framework.zend.com/manual
[installation]: http://framework.zend.com/manual/current/en/ref/installation.html
[composer]: http://getcomposer.org
[ZendSkeletonApplication]: https://github.com/zendframework/ZendSkeletonApplication
