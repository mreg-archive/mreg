phplibaddress
=============

Render addresses according to national addressing standards

Uses *phpcountry* to print country names in multiple languages

Separates the broken down address data from composers, witch render
render the address according to varying standards. (At present only
the swedish addressing standard SS 613401:2011 ed. 3 is supported.)


Installation
------------

Install using composer to automatically satisy dependencies and
using the composer auto-loader.


Usage
-----

    namespace itbz\phplibaddress;
    use itbz\phpcountry\Country;
    use itbz\phplibaddress\Composer\Sv;
    use itbz\phplibaddress\Composer\Breviator;

    $country = new Country;
    $country->setLang('en');
    $addr = new Address($country);

    $addr->setGivenName('Many many names');
    $addr->setSurname('Surnameone Surnametwo');
    $addr->setForm('Mr');
    $addr->setThoroughfare('streetname');
    $addr->setPlot('1');
    $addr->setPostcode('222 22');
    $addr->setTown('city');
    $addr->setCountryCode('se');
    $addr->setCountryOfOrigin('en');

    // Se the entire documentation for all options

    $composer = new Sv(new Breviator);
    $composer->setAddress($addr);

    echo $composer->getValid();

    /*

    Mr Many M N Surnameone Surnametwo
    streetname 1
    SE-222 22 city
    Sweden
     
    */


The complete documentation
--------------------------

http://itbz.github.com/packages/phplibaddress.html