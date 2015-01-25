<?php
/**
 * Mreg bootstrap
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;

/**
 * Bootstrap mreg and return dependency injection container
 *
 * Loads a config file, registers the autoloader, set locale and multibyte
 * encoding, verify PHP settings. This bootstrap can be used both by the webb
 * gateway or by a CLI script.
 *
 * @return \Pimple
 *
 * @throws \Exception if PHP settings are invalid
 *
 * @package mreg
 */
return call_user_func(function()
{
    // Read settings
    $settings = require "config.php";

    // Set app version
    $settings['version'] = '0.1.0';

    // Register autoloader
    require_once $settings['appRoot'] . 'libs/autoload.php';

    // Validate php settings
    if ($settings['applyPhpSettings']) {
        $ini = new \itbz\utils\PhpIniManager();
        if (!$ini->applyFile($settings['appRoot'] . 'php.ini')) {
            throw new \Exception(implode("\n", $ini->getErrors()));
        }
    }

    // Set swedish local
    if (@setlocale(LC_ALL, "sv_SE.utf8", "sv_SE", "sv") === FALSE) {
        trigger_error("Unable to set locale", E_USER_ERROR);
    }

    // Set utf-8 multibyte encoding
    if (mb_internal_encoding('UTF-8') === FALSE) {
        trigger_error("Unable to set multibyte encoding", E_USER_ERROR);
    }

    // Return pimple dependencies
    return include "dependencies.php";
});