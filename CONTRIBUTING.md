CONTRIBUTING
============

Any contribution is welcome.

Unit Tests
----------

The project is test driven and all contributions must be tested following as much as possible the current test structure.

- Each class method must be tested in a test class method into ``test/suites/TDD``
- The test class must be annotated with ``@covers testedClass``


Specification test
------------------

while the unit tests that aim to test if every method does its job, 
the specification tests (``spec``) involves an expected behaviour of the code that is more global.

You can write ``spec`` into ``test/suites/Spec``. These test should never produce code coverage and therefor must 
be annotated with ``@coversNothing``.

Test an issue
-------------

If a test fixes an issue reported on the issue tracker the test must be commented with the issue number:

```php

    /**
     * fixes #5
     */
    public function testTheThing(){

```


Coding standards
----------------

All the sources follows the ``PSR-2`` specification. 
In addition short array syntax is mandatory: ``[]`` instead of ``array()``.

You can check if you code is correct by running: ``test/bin/phpcs.bash emacs`` and you
can perform an automatic code cleaning with the command ``test/bin/phpcbf.bash``.
