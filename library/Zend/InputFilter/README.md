InputFilter Component from ZF2
==============================

This is the InputFilter component for ZF2.

- File issues at https://github.com/zendframework/zf2/issues
- Create pull requests against https://github.com/zendframework/zf2
- Documentation is at http://framework.zend.com/docs

HOW TO CONTRIBUTE
-----------------

The Zend Framework 3 input filter component has been refactored completely. It is nearly 10 times faster than
Zend Framework 2's implementation, and consume 8 times less memory.

This has been possible thanks to two things:

- The implementation now uses SPL RecursiveIteratorIterator, which allow a very efficient recursion that is done in C.
- Some very edge cases were removed.

The last point is an important one: we realized that we added to many features in the original input filter
implementation, that leads to a very complicated and bloated code, that was highly inefficient. To keep this component
fast, please don't submit any add that involve edge cases.

You are encouraged to extend the original implementation so that it can fit your needs instead.

LICENSE
-------

The files in this archive are released under the [Zend Framework
license](http://framework.zend.com/license), which is a 3-clause BSD license.

