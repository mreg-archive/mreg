<?php

// INSERT CONFIGURATION VALUES IN THIS FILES

return array(
    // Path to application root on server. Use trailing /
    'appRoot' => 'APP_ROOT/',

    // WWW path to application. Use trailing /
    'wwwRoot' => '/',
    
    // TRUE if routing should require HTTPS
    'requireHttps' => FALSE,
    
    // MySQL database
    'dbName' => '', // INSERT VALUE
    'dbUser' => '', // INSERT VALUE
    'dbPswd' => '', // INSERT VALUE

    // Directory for variable server content. Use trailing /
    'contentDir' => 'APP_ROOT/var/',

    // Directory to temporary store uploaded files. No trailing /
    'uploadDir' => 'APP_ROOT/var/upload',

    // Directory to temporary store files prepared for download. No trailing /
    'downloadDir' => 'APP_ROOT/var/download',
    
    // Mreg error log file. NOTE: must be writable by apache
    'logFile' => 'APP_ROOT/var/log/mreg.log',

    // TRUE if debug statements should be logged to chrome
    'logChrome' => TRUE,
    
    // TRUE if requested PHP settings should be applied
    'applyPhpSettings' => TRUE
);
