<?php
namespace mreg\Auth;

use Phpass\Hash;
use Phpass\Strength;
use Phpass\Strength\Adapter\Wolfram;


class PasswordTest extends \PHPUnit_Framework_TestCase
{

    function getPassword()
    {
        $hasher = new Hash;
        $strength = new Strength(new Wolfram);

        return new Password($hasher, $strength);
    }


    function testGenerate()
    {
        $pswd = $this->getPassword();
        $pswd->setStrongLimit(10);
        $newPswd =  $pswd->generate();

        $this->assertInternalType('string', $newPswd);
        $this->assertTrue($pswd->isStrong($newPswd));
    }


    function testIsMatch()
    {
        $pswd = $this->getPassword();
        $pswd->setStrongLimit(1);

        $pswd->setPassword('foobar');
        $this->assertTrue($pswd->isMatch('foobar'));
    }


    /**
     * @expectedException \mreg\Exception
     */
    function testSetWeakPassword()
    {
        $pswd = $this->getPassword();
        $pswd->setStrongLimit(1000);
        $pswd->setPassword('a');
    }


    function testSetGetHash()
    {
        $pswd = $this->getPassword();
        $this->assertEmpty($pswd->getHash());

        $pswd->setHash('foobar');
        $this->assertFalse('' == $pswd->getHash());
    }

}