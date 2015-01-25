<?php
namespace mreg\tests;

class RouteMock extends \Aura\Router\Route
{
    public function setValues(array $values)
    {
        $this->values = $values;
    }
    public function setNamePrefix($prefix)
    {
        $this->name_prefix = $prefix;
    }
    public function __construct()
    {
    }
}
