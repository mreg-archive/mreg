<?php
namespace itbz\Cache;

class ApcCacherTest extends TestCase
{
    public function createCache()
    {
        $this->cache = new ApcCacher();
    }
}
