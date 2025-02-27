<?php

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'ProfilePress\\Core\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

require __DIR__ . "/src/Functions/custom-settings-api.php";
require __DIR__ . "/src/Functions/GlobalFunctions.php";
require __DIR__ . "/src/Functions/MSFunctions.php";
require __DIR__ . "/src/Functions/PPressBFnote.php";
require __DIR__ . "/src/Functions/Shogun.php";
require __DIR__ . "/src/Functions/FuseWPAdminNotice.php";