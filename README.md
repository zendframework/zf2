### Welcome to the *Zend Framework 2.1* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
Develop: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.1.4dev*

This is the fourth maintenance release for the version 2.1 series.

DD MMM YYYY

### UPDATES IN 2.1.4

#### Security fix: Query route

The query route was deprecated, as a replacement exists within the HTTP router
itself. You can pass a "query" option to the assemble method containing either
the query string or an array of key-value pairs:

```php
$url = $router->assemble(array(
    'name' => 'foo',
), array(
    'query' => array(
        'page' => 3,
        'sort' => 'DESC',
    ), 
    // or: 'query' => 'page=3&sort=DESC'
));

// via URL helper/plugin:
$rendererOrController->url('foo', array(), array('query' => $request->getQuery()));
```

Additionally, the merging of query parameters into the route match was removed
to avoid potential security issues. Please use the query container of the
request object instead.

For more information on the security vector, please see
[ZF2013-01](http://framework.zend.com/security/ZF2013-01).

#### Security fix: DB platform quoting

Altered `Zend\Db` to throw notices when insecure usage of the following methods
is called: 

- `Zend\Db\Adapter\Platform\*::quoteValue*()`
- `Zend\Db\Sql\*::getSqlString*()`

Fixed `Zend\Db` Platform objects to use driver level quoting when provided, and
throw `E_USER_NOTICE` when not provided.  Added `quoteTrustedValue()` API for
notice-free value quoting.  Fixed all userland quoting in Platform objects to
handle a wider array of escapable characters.

For more information on this security vector, please see
[ZF2013-03](http://framework.zend.com/security/ZF2013-03).

#### Better polyfill support

Better polyfill support in `Zend\Session` and `Zend\Stdlib`. Polyfills
(version-specific class replacements) have caused some issues in the 2.1 series.
In particular, users who were not using Composer were unaware/uncertain about
what extra files needed to be included to load polyfills, and those users who
were generating classmaps were running into issues since the same class was
being generated twice.

New polyfill support was created which does the following:

- New, uniquely named classes were created for each polyfill base.
- A stub class file was created for each class needing polyfill support. A
  conditional is present in each that uses `class_alias` to alias the appropriate
  polyfill base as an import. The stub class then extends the base.
- The `compatibility/autoload.php` files in each component affected was altered
  to trigger an `E_USER_DEPRECATED` error asking the user to remove the require
  statement for the file.

The functionality works with both Composer and ZF2's autoloading support, using
either PSR-0 or classmaps. All typehinting is preserved.

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see [INSTALL.md](INSTALL.md).

### CONTRIBUTING

If you wish to contribute to Zend Framework, please read both the
[CONTRIBUTING.md](CONTRIBUTING.md) and [README-GIT.md](README-GIT.md) file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/archives/subscribe/

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in our GitHub
issue tracker:

https://github.com/zendframework/zf2/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
<fw-announce-subscribe@lists.zend.com>.

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
