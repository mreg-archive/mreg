itbz\Cache: PHP data cache abstraction library
==============================================

itbz\Cache allows you to store variables in optcode cache data stores. For
dependence injection reasons a wrapper object for caching functions is sometimes
necessary. For other reasons swftly swaping between optcode caches using a
uniform interface might be desirable. itbz\Cache solves both these issues.

Currently APC and XCache are supported.

## Usage

    $cache = new \itbz\Cache\Apc();
    
    $cache->set('foo', 'bar');
    
    $cache->has('foo'); // true
    
    echo $cache->get('foo');
    // ouputs 'bar'

## License

Copyright (c) 2012 Hannes Forsgård

Licensed under the MIT license

For the full copyright and license information, please view
the LICENSE file that was distributed with this source code.
