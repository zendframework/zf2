Welcome to the Zend Framework 2.0.0 Release! 

RELEASE INFORMATION
---------------
Zend Framework 2.0.0dev4

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

NEW FEATURES
------------

This snapshot includes:

 - The "Dispatchable" and related interfaces (Zend\Stdlib\Dispatchable,
   MessageDescription, RequestDescription, and ResponseDescription)

 - A fully refactored HTTP component
   - Rewritten URI component, with better and more extensible support
     for an array of different URI schemas, as well as more flexible
     path and parameter decomposition and serialization.

   - Adds HTTP versions of the Stdlib Request and Response interfaces,
     along with full-fledged support for standard HTTP headers.

   - A rewritten HTTP client that consumes Http\Request objects and
     produces Http\Response objects.

   - Two additional HTTP client implementations that provide a
     convenience API around the base HTTP client. One is static, and
     allows for simple one-off requests:

         $response = ClientStatic::get($uri);
         $response = ClientStatic::post(
            $uri, 
            array('foo' => 'bar'), 
            array('Content-Type' => ClientStatic::ENC_URENCODED)
         );

     The other largely mimics the Zend Framework 1.X HTTP client, and
     proxies functionality to the Request object when appropriate.

 - Updated all docbook sources to DocBook 5 formatting standards.

 - Merging of more than 50 pull requests made by community members,
   ranging from one-liner documentation changes to sweeping fixes to the
   testing repository (including fixing most assertions deprecated in
   PHPUnit 3.5.0).

We will be refactoring all components using the HTTP client in an
upcoming milestone to ensure they continue to work, and will also
post a blog entry and documentation page containing tips.

This snapshot should NOT be used in production, as it is considered
pre-pre alpha quality.

SYSTEM REQUIREMENTS
-------------------

Zend Framework 2 requires PHP 5.3 or later. 

INSTALLATION
------------

Please see INSTALL.txt.

CONTRIBUTING
------------

If you wish to contribute to Zend Framework 2.0, please make sure you have
signed a CLA (http://framework.zend.com/cla), and please read both the
README-DEV.txt and README-GIT.txt file.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
