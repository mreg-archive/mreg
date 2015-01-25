<?php
namespace mreg\NullObject;


class AnonymousUserTest extends \PHPUnit_Framework_TestCase
{

    function testIsValidPassword()
    {
        $user = new AnonymousUser;
        $this->assertFalse($user->isValidPassword('foobar'));
    }

}
