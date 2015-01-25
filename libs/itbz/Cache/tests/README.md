Running itbz\Cache tests
========================

To test cache you need to access the library through you webserver. For
this you can use VisualPHPUnit. If you are using composer VisualPHPUnit
will be downloaded in development mode:

    php composer.phar install --dev

Next you tell VisualPHPUnit where your classes are. Open 'bootstrap.php'
in the VisualPHPUnit directory and add your classes in the bootstraps
array near the end of the file.

If you are using composer you can use the composer autoloader:

    $bootstraps = array(
        "{$root}/../../autoload.php",
        "{$root}/../../../tests/TestCase.php"
    );

Lastly you must tell VisualPHPUnit where your classes are.

    'test_directory' => "{$root}/../../../tests"

Now you can point your browser to VisualPHPUnit click on 'Tests' to open
the test dialog and run your tests.
